module.exports = {
  lintOnSave: false,
  devServer: {
    host: 'localhost',
    port: 8081,
    allowedHosts: 'all',
    client: {
      webSocketURL: 'ws://localhost:8081/ws',
    },
    proxy: {
      '/oauth': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
      '/RatingApi': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
    },
  },
};