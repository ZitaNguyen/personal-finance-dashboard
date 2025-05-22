import { useState } from 'react';
import { loginUser } from "../services/api";
import { Link } from 'react-router-dom';

export default function Login() {
    const [form, setForm] = useState({
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
            const response = await loginUser(form);
            if (response.status === 200) {
                setMessage('Login successful! Redirecting...');
            }
            // get token from response and store it
            const token = response.data.token;
            localStorage.setItem('token', token);
            setAuthToken(token);
        } catch (error) {
            if (error.response) {
                const errorMessage = error.response?.data?.message || 'Login failed. Please try again.';
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
                    <h2 className="mb-4 text-center">Login</h2>
                    
                    {message && <div className="alert alert-info text-center mt-3">{message}</div>}
                    
                    <form onSubmit={handleSubmit}>
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
                        <button type="submit" className="btn btn-primary w-100">Login</button>

                        <small className="form-text text-muted mt-3 text-center d-block">
                            Don't have an account? <Link to="/register">Register here</Link>.
                        </small>
                    </form>
                </div>
            </div>
        </div>
    );
}