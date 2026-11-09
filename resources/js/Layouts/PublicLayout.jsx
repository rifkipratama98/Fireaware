import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import { LiquidGlassNavbar } from '@/Components/LiquidGlassNavbar';
import { Footer } from '@/Components/Footer';
import { ToastNotification } from '@/Components/ToastNotification';
import { BrowserNotificationModal } from '@/Components/BrowserNotificationModal';

export function PublicLayout({ children, title }) {
    const [alertModalOpen, setAlertModalOpen] = useState(false);

    return (
        <div className="min-h-screen flex flex-col bg-[#F8F9FA] text-[#1E242B] selection:bg-[#E05A2B]/20 selection:text-[#E05A2B]">
            {title && <Head title={title} />}

            {/* Flagship Liquid Glass Navbar */}
            <LiquidGlassNavbar onOpenAlertModal={() => setAlertModalOpen(true)} />

            {/* Main Content Area */}
            <main className="flex-1 w-full">{children}</main>

            {/* Reusable Toast Notification */}
            <ToastNotification />

            {/* Browser Push Alert Modal */}
            <BrowserNotificationModal
                isOpen={alertModalOpen}
                onClose={() => setAlertModalOpen(false)}
            />

            {/* Footer */}
            <Footer />
        </div>
    );
}

export default PublicLayout;

