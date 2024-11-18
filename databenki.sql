-- Create CLIENT table
CREATE TABLE CLIENT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15)
);

-- Create FIELD table
CREATE TABLE FIELD (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(100),
    client_id INT,
    FOREIGN KEY (client_id) REFERENCES CLIENT(id)
);

-- Create POINT table
CREATE TABLE POINT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    field_id INT,
    FOREIGN KEY (field_id) REFERENCES FIELD(id)
);
