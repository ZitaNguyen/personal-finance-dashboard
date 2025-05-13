import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import Register from './pages/Register';

export default function App() {
  return (
    <Router>
      <nav className='navbar navbar-expand-lg navbar-light bg-light'>
        <div className="container">
          <ul className="navbar-nav">
            <li className="nav-item">
              <Link to="/" className='nav-link'>Home</Link>
            </li>
            <li className="nav-item">
              <Link to="/register" className='nav-link'>Register</Link>
            </li>
          </ul>
        </div>
      </nav>

      <main className="container mt-5">
        <Routes>
          <Route path="/register" element={<Register />} />
        </Routes>
      </main>
    </Router>
  );
}
