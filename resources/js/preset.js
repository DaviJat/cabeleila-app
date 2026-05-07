import { definePreset } from '@primevue/themes';
import Aura from '@primevue/themes/aura';

const CustomPreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '#f4f6f3',
            100: '#e7ece5',
            200: '#cfdacd',
            300: '#abc0a7',
            400: '#83a07e',
            500: '#5a7253',
            600: '#4a5e44',
            700: '#3c4b38',
            800: '#323e2f',
            900: '#2a3428',
            950: '#161c15'
        },
    }
});

export default CustomPreset;