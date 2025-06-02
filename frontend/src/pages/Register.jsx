import { useState } from 'react';
import { registerUser } from "../services/api";
import { useNavigate } from 'react-router-dom';

export default function Register() {
    const [form, setForm] = useState({
        username: '',
        email: '',
        password: '',
    });

    const [message, setMessage] = useState('');
    const navigate = useNavigate();

    const handleChange = (e) => { 
        setForm({...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await registerUser(form);
            if (response.status === 201) {
                setMessage(response.data.message || 'Registration successful! Redirecting to login...');
                setTimeout(() => {
                    navigate('/auth');
                }, 2000); // Redirect after 2 seconds
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
        <div className="row justify-content-center">
            <div className="col-md-6">
                <div className='card p-4 shadow-sm'>
                    <h2 className="mb-4 text-center">Register</h2>
                    
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
                            <label htmlFor="email" className="form-label">Email</label>
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
                            <label htmlFor="password" className="form-label">Password</label>
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
                        <button type="submit" className="btn btn-primary w-100">Register</button>
                    </form>
                </div>
            </div>
        </div>
    );
}