import { useState } from 'react';
import { registerUser } from "../services/api";

export default function Register() {
    const [form, setForm] = useState({
        username: '',
        email: '',
        password: '',
    });

    const [message, setMessage] = useState('');

    const handleChange = (e) => { 
        setForm({...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await registerUser(form);
            if (response.status === 201) {
                setMessage('Registration successful! Please log in.');
            }
        } catch (error) {
            if (error.response) {
                const errorMessage = error.response?.data?.errorMessage || 'Registration failed. Please try again.';
                setMessage(errorMessage);
            } else {
                setMessage('Network error. Please check your connection.');
            }
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <h2>Register</h2>
            <div>
                <label htmlFor="username">Username:</label>
                <input type="text" name="username" value={form.username} onChange={handleChange} required />
            </div>
            <div>
                <label htmlFor="email">Email:</label>
                <input type="email" name="email" value={form.email} onChange={handleChange} required />
            </div>
            <div>
                <label htmlFor="password">Password:</label>
                <input type="password" name="password" value={form.password} onChange={handleChange} required />
            </div>
            <button type="submit">Register</button>
            {message && <p>{message}</p>}
        </form>
    );
}