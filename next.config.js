/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  // Disable ESLint and TypeScript checks during build for the demo platform
  eslint: {
    ignoreDuringBuilds: true,
  },
  typescript: {
    ignoreBuildErrors: true,
  },
};

module.exports = nextConfig;
