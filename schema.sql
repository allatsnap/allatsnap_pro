CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type ENUM('private', 'shared') NOT NULL DEFAULT 'private',
    status ENUM('unused', 'used') NOT NULL DEFAULT 'unused',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    claimed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_claim_account FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    INDEX idx_claim_ip_date (ip_address, claimed_at)
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS ip_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_logs_ip_date (ip_address, created_at)
);

-- Create initial admin account
-- Replace username/password values before production use
INSERT INTO admins (username, password)
VALUES ('admin', '$2y$10$kP67SHgMwhh6SdnryJ6nu.7J8hsM6zQ0tB8Q07cpM8WqGj0Mt6vhu');
-- The hash above corresponds to: ChangeMe123!
