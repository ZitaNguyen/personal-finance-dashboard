import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    port: 3000, // Default port for the development server
    host: true, // Allow access from other devices on the network
    strictPort: true, // Prevents Vite from trying to use another port if 3000 is already in use
    watch: {
      usePolling: true, // Use polling for file watching
    },
  }
})
