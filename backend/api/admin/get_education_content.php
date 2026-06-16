<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();

$data        = json_decode(file_get_contents("php://input"), true);
$procedure_id = $conn->real_escape_string($data["procedure_id"]);
$apt_no       = $conn->real_escape_string($data["apt_no"]);

// Get procedure category
$proc = $conn->query(
    "SELECT name, category, description 
     FROM procedures 
     WHERE id='$procedure_id'"
)->fetch_assoc();

if (!$proc) {
    echo json_encode(["status" => "error", "message" => "Procedure not found"]);
    exit;
}

$category = $proc["category"];
$proc_name = $proc["name"];

// Return different content based on category and appointment number
$content = [];

if ($category === "Endodontic") {
    if ($apt_no == 1) {
        $content = [
            "title" => "About " . $proc_name,
            "h1"    => "What is " . $proc_name . "?",
            "p1"    => "This endodontic procedure treats infection or damage inside the tooth. Your dentist will carefully examine and diagnose your condition before proceeding.",
            "h2"    => "Why is it Needed?",
            "p2"    => "When the pulp inside the tooth becomes infected or damaged, it must be treated to prevent spread of infection and to save your natural tooth.",
            "h3"    => "What to Expect?",
            "p3"    => "You will receive local anaesthesia before the procedure. The dentist will explain each step. Most patients experience minimal discomfort during treatment.",
            "video_id" => "oZU9Wd_cpYY"
        ];
    } else if ($apt_no == 2) {
        $content = [
            "title" => $proc_name . " - Procedure",
            "h1"    => "During the Procedure",
            "p1"    => "The dentist will clean and shape the root canals using specialised instruments. The area will be thoroughly disinfected to remove all bacteria.",
            "h2"    => "Cleaning and Shaping",
            "p2"    => "Special files are used to clean the inside of the root canals. Irrigation solutions help remove debris and kill bacteria inside the tooth.",
            "h3"    => "Sealing the Tooth",
            "p3"    => "After cleaning, the canals are filled with gutta-percha and sealed. A temporary or permanent filling is placed to protect the tooth.",
            "video_id" => "81qSdFYKRcc"
        ];
    } else if ($apt_no == 3) {
        $content = [
            "title" => "Final Restoration",
            "h1"    => "Protecting Your Tooth",
            "p1"    => "After endodontic treatment, the tooth needs a final restoration to protect it. This may be a crown or a permanent filling depending on your procedure.",
            "h2"    => "Why Restoration is Important",
            "p2"    => "The treated tooth becomes more fragile after the procedure. A proper restoration prevents fracture and reinfection, extending the life of your tooth.",
            "h3"    => "Long Term Care",
            "p3"    => "Brush and floss regularly. Avoid very hard foods. Visit your dentist for regular checkups to monitor the treated tooth.",
            "video_id" => "VXTJPFRzkvk"
        ];
    }
} else if ($category === "Restorative") {
    if ($apt_no == 1) {
        $content = [
            "title" => "About " . $proc_name,
            "h1"    => "What is " . $proc_name . "?",
            "p1"    => "This restorative procedure repairs or replaces damaged tooth structure. Your dentist will assess the condition of your tooth and plan the best restoration.",
            "h2"    => "Why is Restoration Needed?",
            "p2"    => "Damaged or decayed teeth need restoration to restore function and appearance. Early treatment prevents further damage and more complex procedures.",
            "h3"    => "What to Expect?",
            "p3"    => "The procedure is performed under local anaesthesia if needed. Your dentist will match the restoration to your natural tooth colour and shape.",
            "video_id" => "oZU9Wd_cpYY"
        ];
    } else if ($apt_no == 2) {
        $content = [
            "title" => $proc_name . " - Procedure",
            "h1"    => "During the Procedure",
            "p1"    => "The dentist will prepare the tooth by removing any decay or damaged structure. The area will be cleaned thoroughly before placing the restoration.",
            "h2"    => "Placing the Restoration",
            "p2"    => "The restoration material is carefully shaped and placed to restore the tooth. Multiple layers may be applied and hardened to achieve the best result.",
            "h3"    => "Final Adjustments",
            "p3"    => "Your bite will be checked and adjusted to ensure comfort. The restoration is polished for a natural appearance and smooth surface.",
            "video_id" => "81qSdFYKRcc"
        ];
    } else if ($apt_no == 3) {
        $content = [
            "title" => "Care After " . $proc_name,
            "h1"    => "Immediate Care",
            "p1"    => "Avoid eating on the treated side for a few hours. If anaesthesia was used, wait until feeling returns before eating to avoid accidentally biting your cheek.",
            "h2"    => "Ongoing Care",
            "p2"    => "Brush gently twice daily with fluoride toothpaste. Floss carefully around the restoration. Avoid very hard or sticky foods that could damage it.",
            "h3"    => "When to Contact Your Dentist",
            "p3"    => "Contact your dentist if you experience persistent pain, sensitivity, or if the restoration feels loose or broken. Regular checkups are important.",
            "video_id" => "VXTJPFRzkvk"
        ];
    }
}

echo json_encode([
    "status"   => "success",
    "category" => $category,
    "content"  => $content
]);
?>