import './bootstrap';
import { initCallBookingPickers } from './call-booking-picker';

// Scroll to hide menu functionality
document.addEventListener('DOMContentLoaded', function() {
    initCallBookingPickers();
    const menu = document.getElementById('main-menu');
    const scrollableDiv = document.querySelector('#main-content');
    let lastScrollTop = 0;
    let isScrolling = false;
    
    // Throttle function to limit scroll event frequency
    function throttle(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Handle scroll event
    function handleScroll() {
        if (isScrolling) return;
        
        isScrolling = true;
        requestAnimationFrame(() => {
            const scrollTop = scrollableDiv.scrollTop;
            const screenHeight = window.innerHeight;
            const scrollPercentage = (scrollTop / screenHeight) * 100;
            
            // Calculate opacity based on scroll percentage
            // At 0% scroll: opacity = 1 (fully visible)
            // At 20% scroll: opacity = 0 (completely hidden)
            let opacity = 1;
            
            if (scrollPercentage >= 20) {
                // Completely hide menu when scrolled 20% or more
                menu.classList.add('hidden');
                opacity = 0;
            } else {
                // Remove hidden class and calculate opacity
                menu.classList.remove('hidden');
                // Linear decrease from 1 to 0 as scroll goes from 0% to 20%
                opacity = Math.max(0, 1 - (scrollPercentage / 20));
            }
            
            // Apply the calculated opacity
            menu.style.opacity = opacity;
            
            lastScrollTop = scrollTop;
            isScrolling = false;
        });
    }
    
    // Add throttled scroll listener to the scrollable div
    if (scrollableDiv) {
        scrollableDiv.addEventListener('scroll', throttle(handleScroll, 10));
    }
});
