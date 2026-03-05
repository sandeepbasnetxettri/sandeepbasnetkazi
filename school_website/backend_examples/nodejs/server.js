const express = require('express');
const mysql = require('mysql2/promise');
const bcrypt = require('bcryptjs');
const bodyParser = require('body-parser');

const app = express();
app.use(bodyParser.json());

// Database Configuration
const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'school_db',
    waitForConnections: true,
    connectionLimit: 10
});

// Auth Route
app.post('/login', async (req, res) => {
    const { username, password, role } = req.body;

    try {
        const [rows] = await pool.execute(
            'SELECT id, username, password, role FROM users WHERE username = ? AND role = ?',
            [username, role]
        );

        const user = rows[0];

        if (!user) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        // Verify password
        const isMatch = await bcrypt.compare(password, user.password);

        // Fallback for plain text (for your current DB state)
        if (!isMatch && password !== user.password) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        res.json({
            message: 'Login successful',
            user: {
                id: user.id,
                username: user.username,
                role: user.role
            }
        });

    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error' });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Node.js server running on port ${PORT}`);
});
