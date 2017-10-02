
module.exports = {
  siteMetadata: {
    title: 'Mannequin.io',
  },
  plugins: [
      'gatsby-plugin-react-helmet',
      {
          resolve: 'gatsby-plugin-postcss-sass',
          options: {
              postCssPlugins: [
                  require('autoprefixer')()
              ]
          }
      },
      {
          resolve: `gatsby-source-filesystem`,
          options: {
              path: `${__dirname}/../docs`,
              name: 'pages',
          },
      },
      {
          resolve: 'gatsby-transformer-remark',
          options: {
              plugins: [
                  'gatsby-remark-autolink-headers',
                  {resolve: `${__dirname}/lib/remarked-strip-md-links`}
              ]
          }
      },
      {
          resolve: 'gatsby-plugin-google-analytics',
          options: {
              trackingId: process.env.GOOGLE_ANALYTICS
          }
      }
  ],
}
