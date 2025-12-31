import js from "@eslint/js";

export default [
  {
    ignores: [
      "bootstrap/cache/**",
      "node_modules/**",
      "public/build/**",
      "storage/**",
      "vendor/**",
    ],
  },
  js.configs.recommended,
  {
    files: ["**/*.js", "**/*.cjs", "**/*.mjs"],
    languageOptions: {
      ecmaVersion: "latest",
      sourceType: "module",
    },
    rules: {
      "no-unused-vars": ["warn", { "argsIgnorePattern": "^_" }],
    },
  },
];
