-- Drop existing tables if they exist
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS wishlist;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS order_items;

-- Create products table
CREATE TABLE products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price INTEGER NOT NULL,
    image_url TEXT,
    featured INTEGER DEFAULT 0,
    sales_count INTEGER DEFAULT 0,
    review_count INTEGER DEFAULT 0,
    rating REAL DEFAULT 5.00,
    stock INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NULL,
    google_id TEXT UNIQUE,
    discord_id TEXT UNIQUE,
    profile_image TEXT DEFAULT 'assets/images/default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create cart table
CREATE TABLE cart (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    product_id INTEGER,
    quantity INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create wishlist table
CREATE TABLE wishlist (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    product_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE(user_id, product_id)
);

-- Create orders table
CREATE TABLE orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    total_amount INTEGER NOT NULL,
    status TEXT CHECK(status IN ('pending', 'processing', 'shipped', 'delivered', 'cancelled')) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create order items table
CREATE TABLE order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER,
    product_id INTEGER,
    quantity INTEGER,
    price INTEGER,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample product data
INSERT INTO products (name, description, price, image_url, featured, sales_count, review_count, stock) VALUES
('Moondrop MAY DSP', 'Full-Frequency Dynamic Driver - Dual Driver Two-way Crossover UWBS Combination Verified Acoustic FEA Optimization', 979000, 'https://i.imgur.com/N5KUj6R.png', 1, 500, 2000, 100),
('TruthEar Hexa', 'Premium In-Ear Monitors with Exceptional Sound Quality', 1241000, 'https://i.imgur.com/cCWvPiN.png', 0, 300, 256, 50),
('SIMGOT ET142', 'Professional Grade Audio Monitoring Earphones', 3636530, 'https://i.imgur.com/NNN2QvU.png', 0, 200, 205, 30),
('7Hz / 7 Hertz SONUS', 'High-Fidelity Audio Experience with Deep Bass', 744960, 'https://i.imgur.com/f60bqSc.png', 0, 400, 991, 80),
('TruthEar GATE', 'Entry-Level Audiophile IEMs with Balanced Sound', 334650, 'https://i.imgur.com/kkHSWTy.png', 0, 600, 364, 150),
('Moondrop Aria', 'Single Dynamic Driver IEM with Premium Metal Shell', 1100000, 'https://i.imgur.com/J0YMOZo.jpeg', 0, 450, 892, 120),
('KZ ZSN Pro X', 'Hybrid Driver Configuration with Enhanced Bass', 299000, 'https://i.imgur.com/jMeJuEv.jpeg', 0, 800, 1500, 200),
('Tin HiFi T2 Plus', 'Neutral Sound Signature with Detailed Mids', 890000, 'https://i.imgur.com/DZDPBuU.jpeg', 0, 300, 450, 75),
('BLON BL-03', 'Warm and Musical Sound Profile', 420000, 'https://i.imgur.com/IxGvBSS.jpeg', 0, 700, 1200, 150),
('Moondrop Chu II', 'Budget King with Premium Sound Quality', 250000, 'https://i.imgur.com/QWEkjBe.jpeg', 0, 1000, 2000, 300),
('Dunu Titan S', 'Reference Class Single Dynamic Driver', 1450000, 'https://i.imgur.com/65fjDA9.jpeg', 0, 200, 350, 50),
('CCA CRA', 'High Value Single Dynamic Driver', 280000, 'https://i.imgur.com/UfMrMAi.jpeg', 0, 600, 900, 120),
('Fiio FD3 Pro', 'Premium Beryllium-Coated Driver', 1890000, 'https://i.imgur.com/tw2pHTo.jpeg', 0, 150, 280, 40),
('Tanchjim Ola', 'Compact Design with Balanced Sound', 690000, 'https://i.imgur.com/BakHBpD.jpeg', 0, 250, 420, 60),
('Shuoer S12 Pro', 'Planar Magnetic Technology', 2100000, 'https://i.imgur.com/5MwsA6L.jpeg', 0, 180, 320, 45),
('Tripowin Lea', 'Smooth and Natural Tonality', 450000, 'https://i.imgur.com/FVQTLRR.jpeg', 0, 400, 680, 90),
('Moondrop SSR', 'Super Spaceship Reference', 550000, 'https://i.imgur.com/OxHMxoD.jpeg', 0, 350, 580, 70),
('Final Audio E3000', 'Premium Build with Refined Sound', 790000, 'https://i.imgur.com/lYOc2A0.jpeg', 0, 280, 460, 65),
('Etymotic ER2SE', 'Studio Reference Sound', 1980000, 'https://i.imgur.com/XAMbYsI.jpeg', 0, 120, 240, 35),
('Thieaudio Legacy 2', 'Hybrid Driver Configuration', 1590000, 'https://i.imgur.com/XEfh2Uw.jpeg', 0, 160, 290, 40),
('Ikko OH1S', 'Premium Hybrid IEM', 1750000, 'https://i.imgur.com/LLq3pmh.jpeg', 0, 140, 260, 45),
('Tin HiFi T3 Plus', 'Enhanced Bass with Clear Treble', 990000, 'https://i.imgur.com/oa0Gx0r.jpeg', 0, 220, 380, 55),
('Moondrop Starfield', 'Carbon Nanotube Dynamic Driver', 1390000, 'https://i.imgur.com/PY7aQYG.jpeg', 0, 420, 780, 85),
('Tangzu Waner S.G 2', 'Arise Greatness Refined', 330000, 'https://i.imgur.com/LBVXbSQ.jpeg', 0, 180, 320, 50),
('Moondrop Blessing 3', 'Triple Hybrid Driver Configuration', 4620000, 'https://i.imgur.com/Deq1IYo.jpeg', 0, 300, 450, 60);

-- Insert demo user
INSERT INTO users (username, email, password, profile_image) VALUES
('demo_user', 'demo@example.com', '$2y$10$example_hash', 'assets/images/default-avatar.png');
