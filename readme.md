# WP-ENV
1. If error occurs when running `wp-env start` -> Your local changes to the following files would be overwritten by checkout. Go to `.wp-env/[hash]/WordPress` and run `git checkout origin/6.9-branch --force` (use latest WP version). Then run again `wp-env start`.
2. Try adding `.gitignore` if above fails
