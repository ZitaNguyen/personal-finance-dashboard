import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import Register from './pages/Register';

export default function App() {
  return (
    <Router>
      <nav>
        <Link to="/register">Register</Link>
      </nav>
      <Routes>
        <Route path="/register" element={<Register />} />
      </Routes>
    </Router>
  );
}
