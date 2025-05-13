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
        <div>
            <h2 className="mb-4">Register</h2>
            
            {message && <div className="alert alert-info text-center mt-3">{message}</div>}
            
            <form onSubmit={handleSubmit}>
                <div className="mb-3">
                    <label htmlFor="username" className="form-label">Username</label>
                    <input 
                        type="text" 
                        className="form-control" 
                        id="username" 
                        name="username" 
                        value={form.username} 
                        onChange={handleChange} 
                        required 
                    />
                </div>
                <div className="mb-3">
                    <label for="email" className="form-label">Email</label>
                    <input 
                        type="email" 
                        className="form-control" 
                        id="email" 
                        name="email"
                        value={form.email} 
                        onChange={handleChange} 
                        required 
                    />
                </div>
                <div className="mb-3">
                    <label for="password" className="form-label">Password</label>
                    <input 
                        type="password" 
                        className="form-control" 
                        id="password" 
                        name="password"
                        value={form.password} 
                        onChange={handleChange} 
                        required 
                    />
                </div>
                <button type="submit" className="btn btn-primary">Register</button>
                
            </form>
        </div>
    );
}