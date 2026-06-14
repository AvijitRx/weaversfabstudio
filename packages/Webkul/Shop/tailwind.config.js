/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/Resources/**/*.blade.php", "./src/Resources/**/*.js"],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1440px",
            },

            padding: {
                DEFAULT: "90px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1240px",
            "2xl": "1440px",
            1180: "1180px",
            1060: "1060px",
            991: "991px",
            868: "868px",
        },

        extend: {
            colors: {
                /*
                 * Weavers Fab Studio palette. `navyBlue` / `lightOrange` keep
                 * their original names because every template references them —
                 * remapping the values rebrands the whole theme at once.
                 */
                navyBlue: "#1D2435",    // ink
                lightOrange: "#F4EEE2", // paper
                darkGreen: '#40994A',
                darkBlue: '#0044F2',
                darkPink: '#F85156',
                madder: "#B23A26",
                madderDeep: "#8F2C1C",
                gold: "#BB8A36",
                goldSoft: "#F0C98A",
                paper: "#F4EEE2",
                cream: "#FAF6EE",
                ivory: "#FCFAF5",
                inkSoft: "#5A6072",
            },

            fontFamily: {
                /*
                 * Same trick for type: templates reference `font-poppins` /
                 * `font-dmserif` everywhere, so the brand fonts map onto
                 * those names.
                 */
                poppins: ["Karla", "Helvetica Neue", "sans-serif"],
                dmserif: ["Fraunces", "Georgia", "serif"],
            },
        }
    },

    plugins: [],

    safelist: [
        {
            pattern: /icon-/,
        }
    ]
};
