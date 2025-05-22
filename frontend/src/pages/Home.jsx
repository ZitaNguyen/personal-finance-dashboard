import { Link } from "react-router-dom";

export default function Home() {
    return (
        <div className="text-center mt-5">
            <h1>Welcome to Your Personal Finance Dashboard</h1>
            <div className="mt-4">
                <Link to="/auth">
                    <button className="btn btn-primary me-2">Login</button>
                </Link>
                <Link to="/register">
                    <button className="btn btn-secondary">Register</button>
                </Link>
            </div>
        </div>
    );
}