## WinSCP - Terminal
1. Remove folder
rm -rf /path/to/directory

2. Export db - mysqldump -u -p DBNAME > backup.sql --no-tablespaces
mysqldump -udbu13616068 '-pAKQGU8zW' db13616068-hesse > backup.sql --no-tablespaces - example uses global db user
a. –no-tablespaces is required for error 'Access denied; you need (at least one of) the PROCESS privilege(s) for this operation
b. -p argument can be left empty and password will be asked afterwards OR wrapped entirely with single quotes to escape special chars in password, e.g. round brackets.

## /* Command Prompt (Windows) */
1. Login to SSH - command prompt
ssh user@host - you can copy the command here
Then asks for password - cannot be copied!
Navigate to directories like - cd www

2. Move db to local, directly type in windows powershell ssh root@ipaddress "mysqldump -u dbuser -p dbname | gzip -9" > dblocal.sql.gz
ssh wp13616068@wp13616068.server-he.de "mysqldump -u dbu13616068 -p db13616068-hesse" > dblocal.sql --no-tablespaces
You will be ask first for SSH pass and then DB pass

3. Move files - local to ftp
scp -r /path/local user@host:/path/ftp/
scp -r D:/Development/Themes/flatsome wp13584349@wp13584349.server-he.de:/is/htdocs/wp13584349_AK66NKDK4T/www/shopkit/wp-content/themes

4. Extract tgz file - navigate to the folder https://www.hostinger.com/tutorials/how-to-extract-or-make-archives-via-ssh/
tar -xvf archive.tar.gz - extracts to the SAME folder where the file is
tar -xvf archive.tar.gz -C /tmp/files - separate folder
5. Search in files
grep -r "text" /full-path - doesn’t matter where are you atm, the path must be fully typed
-i stands for ignore case (optional in your case).
-r stands for recursive.
-l stands for "show the file name, not the result itself".

6. Get folder size du -msh FolderName
du -msh uploads/
du -msh * - shows all files and folder sizes in the current path
du -h * - prints ALL child -> child folders
