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
                    DEFAULT: "#E35A9B",
                    dark: "#D1478A",
                },
            },
            fontFamily: {
                sans: ["Poppins", "ui-sans-serif", "system-ui"],
            },
        },
    },
    plugins: [],
};
