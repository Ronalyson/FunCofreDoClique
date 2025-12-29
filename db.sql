CREATE DATABASE IF NOT EXISTS cofre_clique CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cofre_clique;

CREATE TABLE wallet_state (
  id TINYINT PRIMARY KEY CHECK (id = 1),
  total_cents BIGINT NOT NULL DEFAULT 0,
  total_clicks BIGINT NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO wallet_state (id, total_cents, total_clicks)
VALUES (1, 0, 0)
ON DUPLICATE KEY UPDATE id = 1;

CREATE TABLE clicks (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  ip_hash CHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE withdrawals (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  reason TEXT NOT NULL,
  amount_cents BIGINT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE VIEW withdrawals_view AS
SELECT id,
       name,
       reason,
       amount_cents / 100 AS amount_reais,
       created_at
FROM withdrawals
ORDER BY id DESC;
