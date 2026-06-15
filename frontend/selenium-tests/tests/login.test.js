const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');
const assert = require('assert');

async function runLoginTests() {
  console.log('--- Initializing Selenium Chrome Driver ---');
  
  const options = new chrome.Options();
  // We can add options here, e.g. run headless if needed by passing --headless
  if (process.env.HEADLESS === 'true') {
    options.addArguments('--headless');
  }
  options.addArguments('--no-sandbox');
  options.addArguments('--disable-dev-shm-usage');

  const driver = await new Builder()
    .forBrowser('chrome')
    .setChromeOptions(options)
    .build();

  // Target url corresponds to the migrated React Hash Routing login page
  const baseUrl = process.env.TEST_URL || 'http://localhost/rct-education-web/frontend/index.php#/login';

  try {
    console.log(`Starting E2E Login test suite on target: ${baseUrl}\n`);

    // ----------------------------------------------------
    // Test Case 1: Page Load, Logo, and Language Translators
    // ----------------------------------------------------
    console.log('TC-01: Verifying page loads and language switch translations...');
    await driver.get(baseUrl);

    // Wait for the login screen to render (email input field present)
    await driver.wait(until.elementLocated(By.id('email')), 15000);
    
    // Check initial title / header in English
    let loginHeader = await driver.findElement(By.css('.auth-card h2'));
    let headerText = await loginHeader.getText();
    console.log(`- Login title text (English): "${headerText}"`);
    
    // Switch to Tamil
    console.log('- Clicking Tamil (TA) switcher...');
    const taBtn = await driver.findElement(By.xpath("//button[contains(text(), 'TA')]"));
    await taBtn.click();
    await driver.sleep(1000); // Allow text state updates
    
    let taHeaderText = await loginHeader.getText();
    console.log(`- Login title text (Tamil): "${taHeaderText}"`);
    assert.notStrictEqual(headerText, taHeaderText, 'Language text translation failed to change.');

    // Switch back to English
    console.log('- Reverting back to English (EN)...');
    const enBtn = await driver.findElement(By.xpath("//button[contains(text(), 'EN')]"));
    await enBtn.click();
    await driver.sleep(1000);

    // ----------------------------------------------------
    // Test Case 2: Invalid Credentials Error Message
    // ----------------------------------------------------
    console.log('\nTC-02: Verifying form rejection on invalid user credentials...');
    
    const emailField = await driver.findElement(By.id('email'));
    const passwordField = await driver.findElement(By.id('password'));
    const submitBtn = await driver.findElement(By.css('button[type="submit"]'));

    await emailField.clear();
    await emailField.sendKeys('unknown-patient@domain.com');
    await passwordField.clear();
    await passwordField.sendKeys('invalidpassword');
    
    console.log('- Submitting login form...');
    await submitBtn.click();

    // Wait for the error block to appear
    await driver.wait(until.elementLocated(By.className('auth-error')), 5000);
    const errorMsg = await driver.findElement(By.className('auth-error')).getText();
    console.log(`- Error message shown: "${errorMsg}"`);
    assert.ok(errorMsg.length > 0, 'Error block failed to display validation warning.');

    // ----------------------------------------------------
    // Test Case 3: Successful Authentication & Redirect
    // ----------------------------------------------------
    console.log('\nTC-03: Verifying successful authentication using Admin account...');
    
    // Clear fields
    await emailField.clear();
    await emailField.sendKeys('admin@rct.com');
    await passwordField.clear();
    await passwordField.sendKeys('admin123');
    
    console.log('- Submitting valid credentials...');
    await submitBtn.click();

    // Redirection waits for the admin sidebar/app shell container to render
    console.log('- Waiting for navigation redirection...');
    await driver.wait(until.elementLocated(By.className('admin-sidebar')), 15000);
    
    const currentUrl = await driver.getCurrentUrl();
    console.log(`- Successfully navigated! Current SPA URL is: ${currentUrl}`);
    assert.ok(currentUrl.includes('/admin'), 'Url path did not redirect to admin portal.');

    console.log('\n=========================================');
    console.log('🎉 E2E Frontend Login E2E Tests: Passed! 🎉');
    console.log('=========================================');

  } catch (error) {
    console.error('\nE2E Test Execution failed ❌:', error);
    process.exit(1);
  } finally {
    await driver.quit();
  }
}

runLoginTests();
