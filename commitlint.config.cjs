module.exports = {
    extends: ['@commitlint/config-conventional'],
    ignores: [(message) => /dependabot\[bot\]/.test(message)],
};
