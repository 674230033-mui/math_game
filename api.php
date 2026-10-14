<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

class MathPuzzleDB {
    private $db;

    public function __construct() {
        $dbPath = __DIR__ . '/math_game.sqlite';
        $this->db = new PDO("sqlite:" . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->createTable();
        $this->autoSeed();
    }

    private function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS levels (
            id INTEGER PRIMARY KEY,
            title TEXT NOT NULL,
            question TEXT NOT NULL,
            content TEXT NOT NULL,
            choices TEXT NOT NULL,
            answers TEXT NOT NULL,
            type TEXT NOT NULL
        )";
        $this->db->exec($sql);
    }

    private function autoSeed() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM levels");
        if ($stmt->fetchColumn() == 0) {
            $levels = [
                [1, "ด่านที่ 1: การแก้สมการอย่างง่าย", "หาค่า x จากสมการ x + 5 = 12", "x + 5 = ?", json_encode(["5", "7", "12", "17"]), json_encode(["7"]), "Equation"],
                [2, "ด่านที่ 2: สมการการลบ", "หาค่า x จากสมการ x - 8 = 15", "x - 8 = ?", json_encode(["7", "13", "23", "25"]), json_encode(["23"]), "Equation"],
                [3, "ด่านที่ 3: สมการการคูณ", "หาค่า x จากสมการ 4x = 36", "4 × x = ?", json_encode(["8", "9", "12", "14"]), json_encode(["9"]), "Equation"],
                [4, "ด่านที่ 4: สมการการหาร", "หาค่า x จากสมการ x / 6 = 7", "x ÷ 6 = ?", json_encode(["36", "42", "48", "54"]), json_encode(["42"]), "Equation"],
                [5, "ด่านที่ 5: สมการสองขั้นตอน", "หาค่า x จากสมการ 2x + 3 = 15", "2x + 3 = ?", json_encode(["4", "6", "8", "9"]), json_encode(["6"]), "Equation"],

                [6, "ด่านที่ 6: การบวกเศษส่วน", "คำนวณผลบวก 1/4 + 2/4", "1/4 + 2/4 = ?", json_encode(["2/4", "3/4", "3/8", "1/2"]), json_encode(["3/4"]), "Fraction"],
                [7, "ด่านที่ 7: เศษส่วนอย่างต่ำ", "ทำ 4/8 ให้เป็นเศษส่วนอย่างต่ำ", "4/8 = ?", json_encode(["1/4", "1/2", "2/3", "3/4"]), json_encode(["1/2"]), "Fraction"],
                [8, "ด่านที่ 8: การบวกทศนิยม", "คำนวณ 2.5 + 3.7", "2.5 + 3.7 = ?", json_encode(["5.2", "6.2", "6.5", "7.2"]), json_encode(["6.2"]), "Decimal"],
                [9, "ด่านที่ 9: การลบทศนิยม", "คำนวณ 10.5 - 4.2", "10.5 - 4.2 = ?", json_encode(["6.1", "6.3", "6.5", "7.3"]), json_encode(["6.3"]), "Decimal"],
                [10, "ด่านที่ 10: การคูณทศนิยม", "คำนวณ 0.5 × 8", "0.5 × 8 = ?", json_encode(["3.0", "4.0", "4.5", "5.0"]), json_encode(["4.0"]), "Decimal"],

                [11, "ด่านที่ 11: ทฤษฎีบทพีทาโกรัส", "สามเหลี่ยมมุมฉากมีด้านประกอบมุมฉากยาว 3 และ 4 ด้านตรงข้ามยาวเท่าใด", "c² = 3² + 4² -> c = ?", json_encode(["5", "6", "7", "25"]), json_encode(["5"]), "Geometry"],
                [12, "ด่านที่ 12: การหาด้านประกอบมุมฉาก", "สามเหลี่ยมมุมฉากมีด้านตรงข้ามยาว 10 ด้านประกอบยาว 6 อีกด้านยาวเท่าใด", "10² - 6² = b² -> b = ?", json_encode(["7", "8", "9", "12"]), json_encode(["8"]), "Geometry"],
                [13, "ด่านที่ 13: พื้นที่สี่เหลี่ยมผืนผ้า", "สี่เหลี่ยมกว้าง 5 ยาว 12 มีพื้นที่เท่าใด", "พื้นที่ = กว้าง × ยาว = ?", json_encode(["30", "50", "60", "70"]), json_encode(["60"]), "Geometry"],
                [14, "ด่านที่ 14: พื้นที่สามเหลี่ยม", "สามเหลี่ยมมีฐาน 8 สูง 6 มีพื้นที่เท่าใด", "พื้นที่ = 1/2 × ฐาน × สูง = ?", json_encode(["14", "24", "48", "52"]), json_encode(["24"]), "Geometry"],
                [15, "ด่านที่ 15: เส้นรอบวงกลม", "รัศมี r = 7 ความยาวเส้นรอบวงเท่าใด (ใช้ π ≈ 22/7)", "2 × (22/7) × 7 = ?", json_encode(["22", "44", "88", "154"]), json_encode(["44"]), "Geometry"],

                [16, "ด่านที่ 16: โจทย์ปัญหาเงินออม", "อนันต์มีเงิน 100 บาท ซื้อขนม x บาท เหลือเงิน 35 บาท ขนมราคาเท่าใด", "100 - x = 35 -> x = ?", json_encode(["55", "65", "75", "135"]), json_encode(["65"]), "Expert"],
                [17, "ด่านที่ 17: โจทย์ปัญหาอัตราส่วน", "อัตราส่วนชายต่อหญิงคือ 2:3 ถ้านักเรียนชายมี 10 คน นักเรียนหญิงมีกี่คน", "2/3 = 10/y -> y = ?", json_encode(["12", "15", "18", "20"]), json_encode(["15"]), "Expert"],
                [18, "ด่านที่ 18: โจทย์ปัญหาร้อยละ", "สินค้าราคา 500 บาท ลดราคา 20% จะต้องจ่ายเงินเท่าใด", "500 - (500 × 0.20) = ?", json_encode(["380", "400", "420", "450"]), json_encode(["400"]), "Expert"],
                [19, "ด่านที่ 19: ความน่าจะเป็นเบื้องต้น", "โยนเหรียญ 1 เหรียญ โอกาสเกิดหน้าหัวเป็นเท่าใด", "P(หัว) = ?", json_encode(["1/4", "1/2", "3/4", "1"]), json_encode(["1/2"]), "Expert"],
                [20, "ด่านที่ 20: ค่าเฉลี่ยเลขคณิต", "ชุดข้อมูล 4, 8, 12, 16 มีค่าเฉลี่ยเท่าใด", "(4 + 8 + 12 + 16) / 4 = ?", json_encode(["8", "10", "12", "14"]), json_encode(["10"]), "Expert"]
            ];

            $stmt = $this->db->prepare("INSERT INTO levels (id, title, question, content, choices, answers, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($levels as $lvl) {
                $stmt->execute($lvl);
            }
        }
    }

    public function getAllLevels() {
        $stmt = $this->db->query("SELECT * FROM levels ORDER BY id ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$row) {
            $row['choices'] = json_decode($row['choices']);
            $row['answers'] = json_decode($row['answers']);
        }
        return $results;
    }
}

try {
    $db = new MathPuzzleDB();
    $levels = $db->getAllLevels();
    echo json_encode(["status" => "success", "data" => $levels], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
