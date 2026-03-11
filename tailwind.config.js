export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                pink: {
                    light: "#F9F4F8",
                    DEFAULT: "#9E2469",
                    dark: "#7D1D54",
                },
            },
            fontFamily: {
                sans: ["Poppins", "ui-sans-serif", "system-ui"],
            },
        },
    },
    plugins: [],
};
