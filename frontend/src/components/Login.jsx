import { useState } from 'react';

const apiUrl = import.meta.env.VITE_API_URL;

function Login({ onLogin, onRegister }) {

    const [form, setForm] = useState({
        email: '',
        password: ''
    });

    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleChange = (event) => {
        const { name, value } = event.target;

        setForm({
            ...form,
            [name]: value
        });
    };

    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!form.email || !form.password) {
            setError('Email and password are required');
            return;
        }

        try {
            setLoading(true);
            setError('');

            const response = await fetch(
                `${apiUrl}/auth/login.php`,
                {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(form)
                }
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(
                    result.message || 'Login failed'
                );
            }

            onLogin(result.user);

        } catch (error) {
            setError(error.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="login-container">

            <form
                className="login-form"
                onSubmit={handleSubmit}
            >

                <h1>Login</h1>

                {error && (
                    <p className="error">
                        {error}
                    </p>
                )}

                <div>
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value={form.email}
                        onChange={handleChange}
                        placeholder="Enter email"
                    />
                </div>

                <div>
                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        value={form.password}
                        onChange={handleChange}
                        placeholder="Enter password"
                    />
                </div>

                <button
                    type="submit"
                    disabled={loading}
                >
                    {loading ? 'Logging in...' : 'Login'}
                </button>

                <p className="auth-link">
                    Don't have an account?
                    <button
                        type="button"
                        onClick={onRegister}
                        className="link-button"
                    >
                        Register
                    </button>
                </p>

            </form>

        </div>
    );
}

export default Login;