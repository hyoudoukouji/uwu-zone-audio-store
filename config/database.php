<?php
class Database {
    private $db_file;

    public function __construct() {
        $this->db_file = __DIR__ . '/../database/uwu_zone.db';
    }
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Create SQLite database
            $this->conn = new PDO("sqlite:" . $this->db_file);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables if they don't exist
            $this->createTables();
        } catch(PDOException $e) {
            throw $e;
        }

        return $this->conn;
    }

    private function createTables() {
        // Users table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            email TEXT UNIQUE,
            password TEXT,
            discord_id TEXT UNIQUE,
            profile_image TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Products table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            image_url TEXT,
            featured INTEGER DEFAULT 0,
            sales_count INTEGER DEFAULT 0,
            review_count INTEGER DEFAULT 0,
            rating REAL DEFAULT 5.0,
            stock INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Cart table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS cart (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )");

        // Wishlist table
        $this->conn->exec("CREATE TABLE IF NOT EXISTS wishlist (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (product_id) REFERENCES products(id),
            UNIQUE(user_id, product_id)
        )");

        // Check if products table is empty, if so insert sample data
        $stmt = $this->conn->query("SELECT COUNT(*) FROM products");
        if ($stmt->fetchColumn() == 0) {
            $this->insertSampleData();
        }
    }

    private function insertSampleData() {
        $products = [
            ['name' => 'Moondrop MAY DSP', 'description' => 'Full-Frequency Dynamic Driver - Dual Driver Two-way Crossover UWBS Combination Verified Acoustic FEA Optimization', 'price' => 979000, 'image_url' => 'https://i.imgur.com/N5KUj6R.png', 'featured' => 1, 'sales_count' => 500, 'review_count' => 2000, 'stock' => 100],
            ['name' => 'TruthEar Hexa', 'description' => 'Premium In-Ear Monitors with Exceptional Sound Quality', 'price' => 1241000, 'image_url' => 'https://i.imgur.com/cCWvPiN.png', 'featured' => 0, 'sales_count' => 300, 'review_count' => 256, 'stock' => 50],
            ['name' => 'SIMGOT ET142', 'description' => 'Professional Grade Audio Monitoring Earphones', 'price' => 3636530, 'image_url' => 'https://i.imgur.com/NNN2QvU.png', 'featured' => 0, 'sales_count' => 200, 'review_count' => 205, 'stock' => 30],
            ['name' => '7Hz / 7 Hertz SONUS', 'description' => 'High-Fidelity Audio Experience with Deep Bass', 'price' => 744960, 'image_url' => 'https://i.imgur.com/f60bqSc.png', 'featured' => 0, 'sales_count' => 400, 'review_count' => 991, 'stock' => 80],
            ['name' => 'TruthEar GATE', 'description' => 'Entry-Level Audiophile IEMs with Balanced Sound', 'price' => 334650, 'image_url' => 'https://i.imgur.com/kkHSWTy.png', 'featured' => 0, 'sales_count' => 600, 'review_count' => 364, 'stock' => 150],
            ['name' => 'Moondrop Aria', 'description' => 'Single Dynamic Driver IEM with Premium Metal Shell', 'price' => 1100000, 'image_url' => 'https://i.imgur.com/J0YMOZo.jpeg', 'featured' => 0, 'sales_count' => 450, 'review_count' => 892, 'stock' => 120],
            ['name' => 'KZ ZSN Pro X', 'description' => 'Hybrid Driver Configuration with Enhanced Bass', 'price' => 299000, 'image_url' => 'https://i.imgur.com/jMeJuEv.jpeg', 'featured' => 0, 'sales_count' => 800, 'review_count' => 1500, 'stock' => 200],
            ['name' => 'Tin HiFi T2 Plus', 'description' => 'Neutral Sound Signature with Detailed Mids', 'price' => 890000, 'image_url' => 'https://i.imgur.com/DZDPBuU.jpeg', 'featured' => 0, 'sales_count' => 300, 'review_count' => 450, 'stock' => 75],
            ['name' => 'BLON BL-03', 'description' => 'Warm and Musical Sound Profile', 'price' => 420000, 'image_url' => 'https://i.imgur.com/IxGvBSS.jpeg', 'featured' => 0, 'sales_count' => 700, 'review_count' => 1200, 'stock' => 150],
            ['name' => 'Moondrop Chu II', 'description' => 'Budget King with Premium Sound Quality', 'price' => 250000, 'image_url' => 'https://i.imgur.com/QWEkjBe.jpeg', 'featured' => 0, 'sales_count' => 1000, 'review_count' => 2000, 'stock' => 300],
            ['name' => 'Dunu Titan S', 'description' => 'Reference Class Single Dynamic Driver', 'price' => 1450000, 'image_url' => 'https://i.imgur.com/65fjDA9.jpeg', 'featured' => 0, 'sales_count' => 200, 'review_count' => 350, 'stock' => 50],
            ['name' => 'CCA CRA', 'description' => 'High Value Single Dynamic Driver', 'price' => 280000, 'image_url' => 'https://i.imgur.com/UfMrMAi.jpeg', 'featured' => 0, 'sales_count' => 600, 'review_count' => 900, 'stock' => 120],
            ['name' => 'Fiio FD3 Pro', 'description' => 'Premium Beryllium-Coated Driver', 'price' => 1890000, 'image_url' => 'https://i.imgur.com/tw2pHTo.jpeg', 'featured' => 0, 'sales_count' => 150, 'review_count' => 280, 'stock' => 40],
            ['name' => 'Tanchjim Ola', 'description' => 'Compact Design with Balanced Sound', 'price' => 690000, 'image_url' => 'https://i.imgur.com/BakHBpD.jpeg', 'featured' => 0, 'sales_count' => 250, 'review_count' => 420, 'stock' => 60],
            ['name' => 'Shuoer S12 Pro', 'description' => 'Planar Magnetic Technology', 'price' => 2100000, 'image_url' => 'https://i.imgur.com/5MwsA6L.jpeg', 'featured' => 0, 'sales_count' => 180, 'review_count' => 320, 'stock' => 45],
            ['name' => 'Tripowin Lea', 'description' => 'Smooth and Natural Tonality', 'price' => 450000, 'image_url' => 'https://i.imgur.com/FVQTLRR.jpeg', 'featured' => 0, 'sales_count' => 400, 'review_count' => 680, 'stock' => 90],
            ['name' => 'Moondrop SSR', 'description' => 'Super Spaceship Reference', 'price' => 550000, 'image_url' => 'https://i.imgur.com/OxHMxoD.jpeg', 'featured' => 0, 'sales_count' => 350, 'review_count' => 580, 'stock' => 70],
            ['name' => 'Final Audio E3000', 'description' => 'Premium Build with Refined Sound', 'price' => 790000, 'image_url' => 'https://i.imgur.com/lYOc2A0.jpeg', 'featured' => 0, 'sales_count' => 280, 'review_count' => 460, 'stock' => 65],
            ['name' => 'Etymotic ER2SE', 'description' => 'Studio Reference Sound', 'price' => 1980000, 'image_url' => 'https://i.imgur.com/XAMbYsI.jpeg', 'featured' => 0, 'sales_count' => 120, 'review_count' => 240, 'stock' => 35],
            ['name' => 'Thieaudio Legacy 2', 'description' => 'Hybrid Driver Configuration', 'price' => 1590000, 'image_url' => 'https://i.imgur.com/XEfh2Uw.jpeg', 'featured' => 0, 'sales_count' => 160, 'review_count' => 290, 'stock' => 40],
            ['name' => 'Ikko OH1S', 'description' => 'Premium Hybrid IEM', 'price' => 1750000, 'image_url' => 'https://i.imgur.com/LLq3pmh.jpeg', 'featured' => 0, 'sales_count' => 140, 'review_count' => 260, 'stock' => 45],
            ['name' => 'Tin HiFi T3 Plus', 'description' => 'Enhanced Bass with Clear Treble', 'price' => 990000, 'image_url' => 'https://i.imgur.com/oa0Gx0r.jpeg', 'featured' => 0, 'sales_count' => 220, 'review_count' => 380, 'stock' => 55],
            ['name' => 'Moondrop Starfield', 'description' => 'Carbon Nanotube Dynamic Driver', 'price' => 1390000, 'image_url' => 'https://i.imgur.com/PY7aQYG.jpeg', 'featured' => 0, 'sales_count' => 420, 'review_count' => 780, 'stock' => 85],
            ['name' => 'Tangzu Waner S.G 2', 'description' => 'Arise Greatness Refined', 'price' => 330000, 'image_url' => 'https://i.imgur.com/LBVXbSQ.jpeg', 'featured' => 0, 'sales_count' => 180, 'review_count' => 320, 'stock' => 50],
            ['name' => 'Moondrop Blessing 3', 'description' => 'Triple Hybrid Driver Configuration', 'price' => 4620000, 'image_url' => 'https://i.imgur.com/Deq1IYo.jpeg', 'featured' => 0, 'sales_count' => 300, 'review_count' => 450, 'stock' => 60]
        ];


        $stmt = $this->conn->prepare("
            INSERT INTO products (name, description, price, image_url, featured, sales_count, review_count, stock)
            VALUES (:name, :description, :price, :image_url, :featured, :sales_count, :review_count, :stock)
        ");

        foreach ($products as $product) {
            $stmt->execute($product);
        }
    }
}
?>
