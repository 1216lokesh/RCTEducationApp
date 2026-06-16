/* eslint-disable */
const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');
const assert = require('assert');
const fs = require('fs');
const path = require('path');

async function runE2ETests() {
  console.log('=== INITIALIZING E2E TEST SUITE FOR COMPLETE APP ===');
  
  const options = new chrome.Options();
  if (process.env.HEADLESS === 'true') {
    options.addArguments('--headless');
  }
  options.addArguments('--no-sandbox');
  options.addArguments('--disable-dev-shm-usage');

  const driver = await new Builder()
    .forBrowser('chrome')
    .setChromeOptions(options)
    .build();

  const baseUrl = process.env.TEST_URL || 'http://localhost/rct-education-web/frontend/index.php';
  
  // Generate a unique dynamic patient email for E2E run
  const timestamp = Date.now();
  const patientEmail = `e2e_patient_${timestamp}@rct.com`;
  const patientPassword = 'password123';
  const patientFirstName = 'E2E';
  const patientLastName = `Patient-${timestamp}`;

  console.log(`Generated Test Email: ${patientEmail}`);
  
  const results = {};
  
  // Helper to safely write results and quit
  const writeResultsAndQuit = async (status, err = null) => {
    try {
      const resultsPath = path.join(__dirname, '..', 'results.json');
      fs.writeFileSync(resultsPath, JSON.stringify(results, null, 2));
      console.log(`\nTest results JSON saved successfully at: ${resultsPath}`);
    } catch (fsErr) {
      console.error('Failed to write results JSON:', fsErr);
    }
    await driver.quit();
    if (status === 'fail') {
      if (err) {
        console.error('\nE2E E2E E2E E2E E2E E2E E2E E2E FAIL ❌:', err);
        try {
          const currentUrl = await driver.getCurrentUrl();
          const pageSource = await driver.getPageSource();
          console.error(`Current URL on failure: ${currentUrl}`);
          console.error(`Page Source snippet on failure: ${pageSource.slice(0, 1000)}`);
        } catch (debugErr) {
          console.error('Failed to retrieve debug page status:', debugErr);
        }
      }
      process.exit(1);
    }
  };

  try {
    // ----------------------------------------------------
    // Step 1: Patient Registration (TC-FE-AUTH-01)
    // ----------------------------------------------------
    const tcAuth01 = 'TC-FE-AUTH-01';
    try {
      console.log('\n[E2E-01] Navigating to Registration page...');
      await driver.get(`${baseUrl}#/register`);

      console.log('Filling out patient registration details...');
      await driver.wait(until.elementLocated(By.id('first_name')), 15000);
      await driver.findElement(By.id('first_name')).sendKeys(patientFirstName);
      await driver.findElement(By.id('last_name')).sendKeys(patientLastName);
      await driver.findElement(By.id('email')).sendKeys(patientEmail);
      await driver.findElement(By.id('phone')).sendKeys('9988776655');
      await driver.findElement(By.id('date_of_birth')).sendKeys('1995-05-15');
      
      const genderSelect = await driver.findElement(By.id('gender'));
      await genderSelect.findElement(By.xpath("option[@value='M']")).click();
      
      const langSelect = await driver.findElement(By.id('language'));
      await langSelect.findElement(By.xpath("option[@value='en']")).click();

      await driver.findElement(By.id('password')).sendKeys(patientPassword);
      await driver.findElement(By.id('confirm_password')).sendKeys(patientPassword);

      console.log('Submitting registration form...');
      await driver.findElement(By.css('button[type="submit"]')).click();

      console.log('Waiting for patient dashboard redirect...');
      await driver.wait(until.urlContains('/patient/dashboard'), 15000);
      await driver.wait(until.elementLocated(By.className('user-welcome')), 15000);
      const welcomeText = await driver.findElement(By.className('user-welcome')).getText();
      console.log(`- Dashboard Welcome Text: "${welcomeText}"`);
      assert.ok(welcomeText.includes('E2E'), 'Dashboard header did not load patient name');
      
      results[tcAuth01] = { status: 'Pass', actual: `Successfully registered patient and loaded dashboard: "${welcomeText}"` };
    } catch (err) {
      results[tcAuth01] = { status: 'Fail', actual: `Registration failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // ----------------------------------------------------
    // Step 2: Verify Warning when No Procedure Assigned (TC-FE-PAT-01)
    // ----------------------------------------------------
    const tcPat01 = 'TC-FE-PAT-01';
    try {
      console.log('\n[E2E-02] Verifying dashboard warning for unassigned treatment plan...');
      await driver.wait(until.elementLocated(By.className('no-proc')), 10000);
      const warningText = await driver.findElement(By.className('no-proc')).getText();
      console.log(`- Warning text shown: "${warningText.replace(/\n/g, ' ')}"`);
      assert.ok(warningText.includes('No procedure assigned'), 'No procedure warning not visible');
      
      results[tcPat01] = { status: 'Pass', actual: `Verified warning card text: "${warningText.replace(/\n/g, ' ')}"` };
    } catch (err) {
      results[tcPat01] = { status: 'Fail', actual: `Failed to verify unassigned warning: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // Logout patient
    console.log('Logging out patient...');
    await driver.findElement(By.className('logout-btn')).click();
    await driver.wait(until.urlContains('/login'), 10000);

    // ----------------------------------------------------
    // Step 3: Admin login & Assign Procedure (TC-FE-ADM-03)
    // ----------------------------------------------------
    const tcAdm03 = 'TC-FE-ADM-03';
    try {
      console.log('\n[E2E-03] Logging in as Admin to assign procedure...');
      await driver.get(`${baseUrl}#/login`);
      await driver.wait(until.elementLocated(By.id('email')), 10000);
      await driver.findElement(By.id('email')).sendKeys('admin@rct.com');
      await driver.findElement(By.id('password')).sendKeys('admin123');
      await driver.findElement(By.css('button[type="submit"]')).click();

      console.log('Waiting for admin dashboard interface...');
      await driver.wait(until.urlContains('/admin'), 15000);
      await driver.wait(until.elementLocated(By.className('admin-sidebar')), 15000);

      console.log('Navigating to Patient Registry tab...');
      await driver.findElement(By.xpath("//a[contains(@href, 'admin/patients')]")).click();
      await driver.wait(until.urlContains('/admin/patients'), 10000);
      await driver.wait(until.elementLocated(By.className('search-control')), 10000);

      console.log(`Searching for newly registered patient: ${patientEmail}`);
      const searchBox = await driver.findElement(By.className('search-control'));
      await searchBox.sendKeys(patientEmail);
      await driver.sleep(2000); // wait for client-side search filter

      console.log('Accessing Patient details page...');
      const profileLinkXPath = `//tr[td[contains(text(), '${patientEmail}')]]//a[contains(., 'Profile')]`;
      const profileLink = await driver.wait(until.elementLocated(By.xpath(profileLinkXPath)), 15000);
      await driver.wait(until.elementIsVisible(profileLink), 5000);
      await driver.executeScript("arguments[0].click();", profileLink);

      console.log('Waiting for Patient details profile card to load...');
      await driver.wait(until.urlContains('/admin/patient/'), 15000);
      await driver.wait(until.elementLocated(By.className('profile-meta-list')), 15000);

      console.log('Assigning Procedure ID 1 (Apexification) & Study Group (Intervention)...');
      const procSelect = await driver.findElement(By.xpath("//select[option[contains(text(), 'Choose Procedure')]]"));
      await procSelect.findElement(By.xpath("option[contains(text(), 'Apexification')]")).click();

      const groupSelect = await driver.findElement(By.xpath("//select[option[contains(text(), 'Choose Group')]]"));
      await groupSelect.findElement(By.xpath("option[@value='Intervention']")).click();

      console.log('Submitting procedure assignment update...');
      await driver.findElement(By.xpath("//button[contains(., 'Update Assignment')]")).click();

      // Verify assignment success alert
      await driver.wait(until.elementLocated(By.className('alert-success')), 10000);
      const assignSuccessText = await driver.findElement(By.className('alert-success')).getText();
      console.log(`- Status text: "${assignSuccessText}"`);
      assert.ok(assignSuccessText.includes('success'), 'Failed to assign procedure to patient');
      
      results[tcAdm03] = { status: 'Pass', actual: `Assigned Apexification & Intervention group: "${assignSuccessText}"` };
    } catch (err) {
      results[tcAdm03] = { status: 'Fail', actual: `Failed to assign procedure: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    console.log('Logging out administrator...');
    await driver.findElement(By.className('logout-btn-item')).click();
    await driver.wait(until.urlContains('/login'), 15000);

    // ----------------------------------------------------
    // Step 4: Patient Appointment 1 Flow (Diagnosis)
    // ----------------------------------------------------
    console.log('\n[E2E-04] Logging back in as Patient to complete Appointment 1...');
    await driver.findElement(By.id('email')).sendKeys(patientEmail);
    await driver.findElement(By.id('password')).sendKeys(patientPassword);
    await driver.findElement(By.css('button[type="submit"]')).click();

    await driver.wait(until.urlContains('/patient/dashboard'), 15000);
    await driver.wait(until.elementLocated(By.className('user-welcome')), 15000);
    
    console.log('Asserting procedure is assigned on patient dashboard...');
    const procTitle = await driver.findElement(By.className('proc-title')).getText();
    console.log(`- Active procedure shown: "${procTitle}"`);
    assert.strictEqual(procTitle, 'Apexification', 'Procedure name mismatch on dashboard');

    console.log('Starting Appointment 1 (Diagnosis)...');
    await driver.findElement(By.xpath("//button[contains(., 'Start Appointment 1')]")).click();
    await driver.wait(until.urlContains('/patient/baseline/1'), 15000);

    // TC-FE-PAT-04: Submit baseline questionnaire
    const tcPat04 = 'TC-FE-PAT-04';
    try {
      console.log('Filing baseline questionnaire...');
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[1]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click();

      console.log('Viewing Procedure Info card...');
      await driver.wait(until.urlContains('/patient/procedure-info/1'), 10000);
      await driver.wait(until.elementLocated(By.className('info-block')), 10000);
      
      results[tcPat04] = { status: 'Pass', actual: 'Baseline questionnaire submitted; redirected to procedure info page.' };
    } catch (err) {
      results[tcPat04] = { status: 'Fail', actual: `Baseline submission failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // TC-FE-PAT-05: Access and view educational procedure content
    const tcPat05 = 'TC-FE-PAT-05';
    try {
      await driver.findElement(By.className('survey-submit-btn')).click(); // Next

      console.log('Reading Education material...');
      await driver.wait(until.urlContains('/patient/education/1'), 10000);
      await driver.wait(until.elementLocated(By.className('info-block')), 10000);
      
      results[tcPat05] = { status: 'Pass', actual: 'Procedure info read; successfully accessed education slide panel.' };
    } catch (err) {
      results[tcPat05] = { status: 'Fail', actual: `Accessing education slides failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // TC-FE-PAT-06: Submit anxiety levels
    const tcPat06 = 'TC-FE-PAT-06';
    try {
      await driver.findElement(By.className('survey-submit-btn')).click(); // Take Quiz

      console.log('Completing anxiety scale survey...');
      await driver.wait(until.urlContains('/patient/anxiety/1'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      for (let i = 1; i <= 5; i++) {
        await driver.findElement(By.xpath(`(//input[@name='q${i}'])[1]`)).click();
      }
      await driver.findElement(By.className('survey-submit-btn')).click(); // Next
      
      results[tcPat06] = { status: 'Pass', actual: 'Anxiety scale scores submitted successfully.' };
    } catch (err) {
      results[tcPat06] = { status: 'Fail', actual: `Anxiety submission failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // TC-FE-PAT-07: Complete knowledge quiz
    const tcPat07 = 'TC-FE-PAT-07';
    try {
      console.log('Taking Knowledge Quiz (answering correctly)...');
      await driver.wait(until.urlContains('/patient/quiz/1'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[2]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[2]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[2]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[2]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click(); // Submit

      console.log('Dismissing Knowledge Quiz overlay modal...');
      await driver.wait(until.elementLocated(By.className('modal-btn')), 10000);
      const scoreText = await driver.findElement(By.className('modal-score')).getText();
      console.log(`- Quiz Score text: "${scoreText}"`);
      assert.ok(scoreText.includes('3 / 3'), 'Quiz score was not 3/3');
      await driver.findElement(By.className('modal-btn')).click(); // Continue
      
      results[tcPat07] = { status: 'Pass', actual: `Quiz answers submitted successfully. Score recorded: "${scoreText}"` };
    } catch (err) {
      results[tcPat07] = { status: 'Fail', actual: `Quiz completion failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    console.log('Reading Clinical Counselling resources...');
    await driver.wait(until.urlContains('/patient/counselling'), 10000);
    await driver.wait(until.elementLocated(By.className('info-block')), 10000);
    await driver.findElement(By.className('survey-submit-btn')).click(); // Proceed to Consent

    // TC-FE-PAT-02: Submit patient digital consent form
    const tcPat02 = 'TC-FE-PAT-02';
    try {
      console.log('Signing digital consent form...');
      await driver.wait(until.urlContains('/patient/consent'), 10000);
      await driver.wait(until.elementLocated(By.css("input[type='checkbox']")), 10000);
      await driver.findElement(By.css("input[type='checkbox']")).click();
      await driver.findElement(By.className('survey-submit-btn')).click(); // Agree
      
      results[tcPat02] = { status: 'Pass', actual: 'Checked digital agreement consent box and submitted.' };
    } catch (err) {
      results[tcPat02] = { status: 'Fail', actual: `Consent signoff failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // TC-FE-PAT-03: Submit app satisfaction survey
    const tcPat03 = 'TC-FE-PAT-03';
    try {
      console.log('Submitting Application Satisfaction questionnaire...');
      await driver.wait(until.urlContains('/patient/satisfaction'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      for (let i = 1; i <= 5; i++) {
        await driver.findElement(By.xpath(`(//input[@name='q${i}'])[1]`)).click();
      }
      await driver.findElement(By.className('survey-submit-btn')).click(); // Next

      // Verify progress count on dashboard is 1
      console.log('Verifying Appointment 1 completed on Patient Dashboard...');
      await driver.wait(until.urlContains('/patient/dashboard'), 15000);
      await driver.wait(until.elementLocated(By.className('progress-count')), 15000);
      let progressCount = await driver.findElement(By.className('progress-count')).getText();
      console.log(`- Progress text: "${progressCount}"`);
      assert.strictEqual(progressCount, '1 / 4 Completed', 'Apt 1 did not increment progress count');
      
      results[tcPat03] = { status: 'Pass', actual: `Satisfaction scores completed. Patient Dashboard status: "${progressCount}"` };
    } catch (err) {
      results[tcPat03] = { status: 'Fail', actual: `Satisfaction survey failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // ----------------------------------------------------
    // Step 5: Patient Appointment 2 Flow (RCT Procedure) (TC-FE-PAT-08)
    // ----------------------------------------------------
    const tcPat08 = 'TC-FE-PAT-08';
    try {
      console.log('\n[E2E-05] Starting Appointment 2 (RCT Procedure)...');
      await driver.findElement(By.xpath("//button[contains(., 'Start Appointment 2')]")).click();
      await driver.wait(until.urlContains('/patient/baseline/2'), 15000);

      console.log('Filing baseline questionnaire...');
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[1]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click();

      console.log('Reading Education material...');
      await driver.wait(until.urlContains('/patient/education/2'), 10000);
      await driver.wait(until.elementLocated(By.className('info-block')), 10000);
      await driver.findElement(By.className('survey-submit-btn')).click(); // Take Quiz

      console.log('Completing anxiety scale survey...');
      await driver.wait(until.urlContains('/patient/anxiety/2'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      for (let i = 1; i <= 5; i++) {
        await driver.findElement(By.xpath(`(//input[@name='q${i}'])[1]`)).click();
      }
      await driver.findElement(By.className('survey-submit-btn')).click(); // Next

      console.log('Taking Knowledge Quiz (answering correctly)...');
      await driver.wait(until.urlContains('/patient/quiz/2'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[2]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[2]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[2]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click(); // Submit

      console.log('Dismissing modal...');
      await driver.wait(until.elementLocated(By.className('modal-btn')), 10000);
      await driver.findElement(By.className('modal-btn')).click(); // Continue

      console.log('Viewing Post-Op instructions...');
      await driver.wait(until.urlContains('/patient/postop'), 10000);
      await driver.wait(until.elementLocated(By.className('info-block')), 10000);
      await driver.findElement(By.className('survey-submit-btn')).click(); // Proceed to Dashboard

      // Verify progress count on dashboard is 2
      console.log('Verifying progress...');
      await driver.wait(until.urlContains('/patient/dashboard'), 15000);
      await driver.wait(until.elementLocated(By.className('progress-count')), 15000);
      const progressCount = await driver.findElement(By.className('progress-count')).getText();
      console.log(`- Progress text: "${progressCount}"`);
      assert.strictEqual(progressCount, '2 / 4 Completed', 'Apt 2 did not increment progress count');
      
      results[tcPat08] = { status: 'Pass', actual: `Post-op instructions read; patient dashboard progress incremented to: "${progressCount}"` };
    } catch (err) {
      results[tcPat08] = { status: 'Fail', actual: `Appointment 2 / Post-op flow failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // ----------------------------------------------------
    // Step 6: Patient Appointment 3 Flow (Crown)
    // ----------------------------------------------------
    const tcPat09 = 'TC-FE-PAT-09';
    try {
      console.log('\n[E2E-06] Starting Appointment 3 (Crown/Final Restoration)...');
      await driver.findElement(By.xpath("//button[contains(., 'Start Appointment 3')]")).click();
      await driver.wait(until.urlContains('/patient/baseline/3'), 15000);

      console.log('Filing baseline questionnaire...');
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[1]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click();

      console.log('Reading Education material...');
      await driver.wait(until.urlContains('/patient/education/3'), 10000);
      await driver.wait(until.elementLocated(By.className('info-block')), 10000);
      await driver.findElement(By.className('survey-submit-btn')).click(); // Take Quiz

      console.log('Completing anxiety scale survey...');
      await driver.wait(until.urlContains('/patient/anxiety/3'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      for (let i = 1; i <= 5; i++) {
        await driver.findElement(By.xpath(`(//input[@name='q${i}'])[1]`)).click();
      }
      await driver.findElement(By.className('survey-submit-btn')).click(); // Next

      console.log('Taking Knowledge Quiz (answering correctly)...');
      await driver.wait(until.urlContains('/patient/quiz/3'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[2]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[1]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click(); // Submit

      console.log('Dismissing modal...');
      await driver.wait(until.elementLocated(By.className('modal-btn')), 10000);
      await driver.findElement(By.className('modal-btn')).click(); // Continue

      // Verify progress count on dashboard is 3
      console.log('Verifying progress...');
      await driver.wait(until.urlContains('/patient/dashboard'), 15000);
      await driver.wait(until.elementLocated(By.className('progress-count')), 15000);
      const progressCount = await driver.findElement(By.className('progress-count')).getText();
      console.log(`- Progress text: "${progressCount}"`);
      assert.strictEqual(progressCount, '3 / 4 Completed', 'Apt 3 did not increment progress count');
      
      results[tcPat09] = { status: 'Pass', actual: `Appointment 3 completed. Dashboard progress: "${progressCount}"` };
    } catch (err) {
      results[tcPat09] = { status: 'Fail', actual: `Appointment 3 flow failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // ----------------------------------------------------
    // Step 7: Patient Appointment 4 Flow (Follow Up)
    // ----------------------------------------------------
    const tcPat10 = 'TC-FE-PAT-10';
    try {
      console.log('\n[E2E-07] Starting Appointment 4 (Follow Up Visit)...');
      await driver.findElement(By.xpath("//button[contains(., 'Start Follow Up')]")).click();
      await driver.wait(until.urlContains('/patient/baseline/4'), 15000);

      console.log('Filing baseline questionnaire...');
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[1]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click();

      console.log('Reading Education material...');
      await driver.wait(until.urlContains('/patient/education/4'), 10000);
      await driver.wait(until.elementLocated(By.className('info-block')), 10000);
      await driver.findElement(By.className('survey-submit-btn')).click(); // Final Assessment

      console.log('Taking Knowledge Quiz (answering correctly)...');
      await driver.wait(until.urlContains('/patient/quiz/4'), 10000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 10000);
      await driver.findElement(By.xpath("(//input[@name='q1'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q2'])[1]")).click();
      await driver.findElement(By.xpath("(//input[@name='q3'])[1]")).click();
      await driver.findElement(By.className('survey-submit-btn')).click(); // Submit (should redirect to followup-1week)

      console.log('Filling 1-Week Follow Up Survey...');
      await driver.wait(until.urlContains('/patient/followup-1week'), 15000);
      await driver.wait(until.elementLocated(By.xpath("(//input[@name='q1'])[1]")), 15000);
      for (let i = 1; i <= 3; i++) {
        await driver.findElement(By.xpath(`(//input[@name='q${i}'])[1]`)).click();
      }
      await driver.findElement(By.className('survey-submit-btn')).click(); // Submit

      // Verify progress count on dashboard is 4
      console.log('Verifying final progress...');
      await driver.wait(until.urlContains('/patient/dashboard'), 15000);
      await driver.wait(until.elementLocated(By.className('progress-count')), 15000);
      const progressCount = await driver.findElement(By.className('progress-count')).getText();
      console.log(`- Progress text: "${progressCount}"`);
      assert.strictEqual(progressCount, '4 / 4 Completed', 'Follow-up did not complete all 4 parts');
      
      results[tcPat10] = { status: 'Pass', actual: `Appointment 4 Follow Up survey completed. Final progress: "${progressCount}"` };
    } catch (err) {
      results[tcPat10] = { status: 'Fail', actual: `Appointment 4 follow up failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    console.log('Logging out patient...');
    await driver.findElement(By.className('logout-btn')).click();
    await driver.wait(until.urlContains('/login'), 15000);

    // ----------------------------------------------------
    // Step 8: Admin Verification of Logged Clinical Data (TC-FE-ADM-01 / TC-FE-ADM-02)
    // ----------------------------------------------------
    const tcAdm01 = 'TC-FE-ADM-01';
    try {
      console.log('\n[E2E-08] Log in as Admin to verify clinical outputs...');
      await driver.findElement(By.id('email')).sendKeys('admin@rct.com');
      await driver.findElement(By.id('password')).sendKeys('admin123');
      await driver.findElement(By.css('button[type="submit"]')).click();

      await driver.wait(until.urlContains('/admin'), 15000);
      await driver.wait(until.elementLocated(By.className('admin-sidebar')), 15000);
      await driver.findElement(By.xpath("//a[contains(@href, 'admin/patients')]")).click();
      await driver.wait(until.urlContains('/admin/patients'), 10000);
      await driver.wait(until.elementLocated(By.className('search-control')), 10000);

      console.log('Filtering registry table...');
      await driver.findElement(By.className('search-control')).sendKeys(patientEmail);
      await driver.sleep(2000); // wait for dynamic filter

      console.log('Opening Patient Detail Page...');
      const detailsLinkXPath = `//tr[td[contains(text(), '${patientEmail}')]]//a[contains(., 'Profile')]`;
      const detailsLink = await driver.wait(until.elementLocated(By.xpath(detailsLinkXPath)), 15000);
      await driver.wait(until.elementIsVisible(detailsLink), 5000);
      await driver.executeScript("arguments[0].click();", detailsLink);

      console.log('Validating logged outcomes on clinical dashboard...');
      await driver.wait(until.urlContains('/admin/patient/'), 15000);
      await driver.wait(until.elementLocated(By.className('profile-meta-list')), 15000);

      console.log('Verifying Quiz Scores...');
      const quiz1Select = await driver.findElement(By.xpath("//div[span[contains(text(), 'Appointment 1 Quiz score')]]//select"));
      const quiz1Value = await quiz1Select.getAttribute('value');
      console.log(`- Quiz 1 score dropdown value: "${quiz1Value}"`);
      assert.strictEqual(quiz1Value, '3', 'Quiz 1 score should be 3/3');

      const quiz2Select = await driver.findElement(By.xpath("//div[span[contains(text(), 'Appointment 2 Quiz score')]]//select"));
      const quiz2Value = await quiz2Select.getAttribute('value');
      console.log(`- Quiz 2 score dropdown value: "${quiz2Value}"`);
      assert.strictEqual(quiz2Value, '3', 'Quiz 2 score should be 3/3');

      const quiz3Select = await driver.findElement(By.xpath("//div[span[contains(text(), 'Appointment 3 Quiz score')]]//select"));
      const quiz3Value = await quiz3Select.getAttribute('value');
      console.log(`- Quiz 3 score dropdown value: "${quiz3Value}"`);
      assert.strictEqual(quiz3Value, '3', 'Quiz 3 score should be 3/3');

      console.log('Verifying Attendance Records...');
      const apt1BtnClass = await driver.findElement(By.xpath("//div[span[contains(text(), 'Appointment 1')]]//button")).getAttribute('class');
      assert.ok(apt1BtnClass.includes('btn-success'), 'Apt 1 should be Present');
      
      const apt2BtnClass = await driver.findElement(By.xpath("//div[span[contains(text(), 'Appointment 2')]]//button")).getAttribute('class');
      assert.ok(apt2BtnClass.includes('btn-success'), 'Apt 2 should be Present');

      const apt3BtnClass = await driver.findElement(By.xpath("//div[span[contains(text(), 'Appointment 3')]]//button")).getAttribute('class');
      assert.ok(apt3BtnClass.includes('btn-success'), 'Apt 3 should be Present');

      const apt4BtnClass = await driver.findElement(By.xpath("//div[span[contains(text(), 'Follow Up Visit')]]//button")).getAttribute('class');
      assert.ok(apt4BtnClass.includes('btn-success'), 'Apt 4 should be Present');

      console.log('Verifying Digital Consent Sign-off...');
      const consentHeader = await driver.findElement(By.xpath("//span[contains(text(), 'Signed')]")).getText();
      console.log(`- Consent Status: "${consentHeader}"`);
      assert.ok(consentHeader.includes('Signed'), 'Consent should be signed');

      console.log('\n======================================================');
      console.log('🎉 E2E COMPLETE USER JOURNEY TEST SUITE PASSED! 🎉');
      console.log('======================================================');
      
      results[tcAdm01] = { status: 'Pass', actual: 'Admin loaded details; verified all attendance marked Present, quiz scores 3/3, and digital consent Signed.' };
    } catch (err) {
      results[tcAdm01] = { status: 'Fail', actual: `Admin outcomes verification failed: ${err.message}` };
      await writeResultsAndQuit('fail', err);
      return;
    }

    // Success Exit
    await writeResultsAndQuit('success');

  } catch (error) {
    await writeResultsAndQuit('fail', error);
  }
}

runE2ETests();
