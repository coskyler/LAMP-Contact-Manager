USE contact_manager_app;

INSERT INTO Users (id, first_name, last_name, username, password)
VALUES (1, 'Test', 'User', 'testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO Contacts (user_id, first_name, last_name, email, phone_number)
VALUES
    (1, 'John', 'Doe', 'john.doe@example.com', '123-456-7890'),
    (1, 'Jane', 'Smith', 'jane.smith@example.com', '987-654-3210'),
    (1, 'Peter', 'Jones', 'peter.jones@work.net', '555-123-4567');