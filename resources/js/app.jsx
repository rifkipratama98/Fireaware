import '../css/app.css';
import React from 'react';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'FireAware Kalimantan';

class AppErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        console.error('FireAware React Error Boundary caught:', error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#1E242B', color: '#F8F9FA', fontFamily: 'sans-serif', padding: '24px' }}>
                    <div style={{ maxWidth: '600px', background: 'rgba(255,255,255,0.05)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '16px', padding: '32px' }}>
                        <h2 style={{ fontSize: '20px', fontWeight: 'bold', color: '#E05A2B', marginBottom: '12px' }}>Terjadi Kendala Tampilan</h2>
                        <p style={{ fontSize: '14px', color: '#cbd5e1', marginBottom: '16px', lineHeight: '1.6' }}>
                            Aplikasi mengalami error pada komponen frontend. Silakan muat ulang halaman atau periksa konsol browser.
                        </p>
                        <pre style={{ fontSize: '12px', background: '#0f172a', padding: '12px', borderRadius: '8px', color: '#f87171', overflowX: 'auto', marginBottom: '20px' }}>
                            {this.state.error?.message || String(this.state.error)}
                        </pre>
                        <button
                            type="button"
                            onClick={() => window.location.reload()}
                            style={{ padding: '10px 20px', background: '#E05A2B', color: '#fff', border: 'none', borderRadius: '8px', fontWeight: 'bold', cursor: 'pointer' }}
                        >
                            Muat Ulang Halaman
                        </button>
                    </div>
                </div>
            );
        }
        return this.props.children;
    }
}

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];
        if (!page) {
            throw new Error(`Halaman Inertia tidak ditemukan: ./Pages/${name}.jsx`);
        }
        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <AppErrorBoundary>
                <App {...props} />
            </AppErrorBoundary>
        );
    },
    progress: {
        color: '#E05A2B',
        showSpinner: false,
    },
});

