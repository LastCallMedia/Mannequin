
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
              path: `${__dirname}/src/docs`,
              name: 'docs',
          },
      },
      // @todo: Move extension documentation to README.md for each extension.
      {
          resolve: `gatsby-source-filesystem`,
          options: {
              path: `${__dirname}/src/extensions`,
              name: 'extensions',
          }
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
  ],
}
