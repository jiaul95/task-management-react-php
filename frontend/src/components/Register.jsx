import { useState } from 'react';

function Register({ onRegister }) {

    const [form, setForm] = useState({
        name: '',
        email: '',
        password: ''
    });

    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const apiUrl = import.meta.env.VITE_API_URL;

    const handleChange = (event) => {
        const { name, value } = event.target;

        setForm({
            ...form,
            [name]: value
        });
    };

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!form.name || !form.email || !form.password) {
            setError('All fields are required');
            return;
        }

        if (form.password.length < 8) {
            setError('Password must be at least 8 characters');
            return;
        }

        if (!/[A-Z]/.test(form.password)) {
            setError('Password must contain at least one uppercase letter');
            return;
        }

        if (!/[a-z]/.test(form.password)) {
            setError('Password must contain at least one lowercase letter');
            return;
        }

        if (!/[0-9]/.test(form.password)) {
            setError('Password must contain at least one number');
            return;
        }

        if (!/[^A-Za-z0-9]/.test(form.password)) {
            setError('Password must contain at least one special character');
            return;
        }

        try {
            setLoading(true);
            setError('');

            const response = await fetch(
                `${apiUrl}/auth/register.php`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(form)
                }
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(
                    result.message || 'Registration failed'
                );
            }

            setForm({
                name: '',
                email: '',
                password: ''
            });

            onRegister();

        } catch (error) {
            setError(error.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="login-container">

            <form className="login-form" onSubmit={handleSubmit}>

                <h1>Register</h1>

                {error && (
                    <p className="error">
                        {error}
                    </p>
                )}

                <div>
                    <label>Name</label>

                    <input
                        type="text"
                        name="name"
                        value={form.name}
                        onChange={handleChange}
                        placeholder="Enter your name"
                    />
                </div>

                <div>
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value={form.email}
                        onChange={handleChange}
                        placeholder="Enter your email"
                    />
                </div>

                <div>
                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        value={form.password}
                        onChange={handleChange}
                        placeholder="Enter your password"
                    />
                </div>

                <button
                    type="submit"
                    disabled={loading}
                >
                    {loading ? 'Registering...' : 'Register'}
                </button>

                <p className="auth-link">
                    Already have an account?
                    <button
                        type="button"
                        onClick={onRegister}
                        className="link-button"
                    >
                        Login
                    </button>
                </p>

            </form>

        </div>
    );
}

export default Register;