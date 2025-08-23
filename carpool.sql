CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15),
    password VARCHAR(255),
    user_type ENUM('driver', 'passenger')
);

CREATE TABLE vehicles (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_number VARCHAR(20),
    model VARCHAR(50),
    seating_capacity INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE rides (
    ride_id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT,
    vehicle_id INT,
    source VARCHAR(100),
    destination VARCHAR(100),
    ride_date DATETIME,
    seats_available INT,
    fare DECIMAL(10,2),
    FOREIGN KEY (driver_id) REFERENCES users(user_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id)
);

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    ride_id INT,
    passenger_id INT,
    status VARCHAR(20),
    FOREIGN KEY (ride_id) REFERENCES rides(ride_id),
    FOREIGN KEY (passenger_id) REFERENCES users(user_id)
);

CREATE OR REPLACE TRIGGER trg_increase_seats_on_cancel
AFTER DELETE ON bookings
FOR EACH ROW
BEGIN
    UPDATE rides
    SET seats_available = seats_available + 1
    WHERE ride_id = :OLD.ride_id;
END;
/
