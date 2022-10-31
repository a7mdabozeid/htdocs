const path = require("path");
const TerserPlugin = require("terser-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const { NODE_ENV = "production" } = process.env;

const externals = {
  react: { this: ["acfwpElements", "element"] },
};

module.exports = {
  entry: "./src/index.tsx",
  mode: NODE_ENV,
  devtool: "source-map",
  watch: NODE_ENV === "development",
  externals,
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: "ts-loader",
        exclude: /node_modules/,
      },
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
    ],
  },
  resolve: {
    extensions: [".tsx", ".ts", ".js"],
  },
  output: {
    filename: "lpfw-admin-app.js",
    path: path.resolve(__dirname, "dist"),
  },
  optimization: {
    minimizer: [new CssMinimizerPlugin(), new TerserPlugin()],
  },
  plugins: [
    new TerserPlugin(),
    new MiniCssExtractPlugin({ filename: "lpfw-admin-app.css" }),
  ],
};
