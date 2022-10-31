const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const rtlCss = require("gulp-rtlcss");
const rename = require("gulp-rename");


const dist_path_main  = './new';
const dist_path = {
    main: dist_path_main,
    mobile: `${dist_path_main}/mobile`,
    custom: `${dist_path_main}/css`,  
  }

  


function compaileSass(cb) {
  return gulp
    .src("./sass/custom.scss")
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(gulp.dest(dist_path.custom))
    .pipe(rtlCss())
    .pipe(rename({ suffix: "-rtl" }))
    .pipe(gulp.dest(dist_path.custom));
}

function compailStyle(cb) {
  return gulp
    .src(["./sass/style.scss", "./sass/woocommerce.scss"])
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(gulp.dest(dist_path.main))
    .pipe(rtlCss())
    .pipe(rename({ suffix: "-rtl" }))
    .pipe(gulp.dest(dist_path.main));
}

function mobile(cb) {
  return gulp
    .src("./sass/mobile-style.scss")
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(rename("style.css"))
    .pipe(gulp.dest(dist_path.mobile))
    .pipe(rtlCss())
    .pipe(rename({ suffix: "-rtl" }))
    .pipe(gulp.dest(dist_path.mobile));
}
function mobile_woo(cb) {
  return gulp
    .src("./sass/mobile-woocommerce.scss")
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    .pipe(rename("woocommerce.css"))
    .pipe(gulp.dest(dist_path.mobile))
    .pipe(rtlCss())
    .pipe(rename({ suffix: "-rtl" }))
    .pipe(gulp.dest(dist_path.mobile));
}

function _watch() {
  gulp.watch("./sass/custom.scss", compaileSass);
  gulp.watch(
    [
      "./sass/style.scss",
      "./sass/woocommerce.scss",
    ],
    compailStyle
  );
  gulp.watch(
    [
      
      "./sass/mobile-style.scss",
      "./sass/mobile-woocommerce.scss",
    ],
    mobile
  );
}
exports.mobile = mobile;
exports.mobile_woo = mobile_woo;
exports.default = gulp.series(
  compaileSass,
   compailStyle,
   mobile,
   mobile_woo,
    _watch
);
