import tailwindcss from "@tailwindcss/vite";
import { globSync } from "glob";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.scss",
                // Get all files in "resources/js" directory
                ...globSync("resources/js/*.js"),
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
