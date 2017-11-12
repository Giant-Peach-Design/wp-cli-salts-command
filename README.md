WP-CLI Salt Command
===================

[![Package license](https://img.shields.io/packagist/l/salaros/wp-cli-salts-command.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Packagist type](https://img.shields.io/badge/Packagist-wp--cli--package-pink.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Packagist downloads](https://img.shields.io/packagist/dt/salaros/wp-cli-salts-command.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Monthly Downloads](https://poser.pugx.org/salaros/wp-cli-salts-command/d/monthly)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Latest Stable Version](https://img.shields.io/packagist/v/salaros/wp-cli-salts-command.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![composer.lock](https://poser.pugx.org/salaros/wp-cli-salts-command/composerlock)](https://packagist.org/packages/salaros/wp-cli-salts-command)

Manage salts for your WordPress installation

## Output salts to STDOUT

```bash
wp salts generate # defaults to --format=php
wp salts generate --format=env
wp salts generate --format=yaml
```

This will grab new salts from the [WordPress Salt API](https://api.wordpress.org/secret-key/1.1/salt/) and output it to the `STDOUT`

## Output salts to a file

### PHP file

```bash
wp salts generate --file=/absolute/path/to/file.php
```

This will output the salts to a file. Because the file contains the complete `define()` code the salts will be set by a simple `require` somewhere in your wp-config.php

### Environment variable file

```bash
wp salts generate --file=/absolute/path/to/.env
```

This will output the salts as shell environment variables (`MY_VAR=VALUE`). Useful for projects that [load configurations from .env files](https://github.com/vlucas/phpdotenv).

### Yaml config file

```bash
wp salts generate --file=/absolute/path/to/file.yaml
```

This will output the salts as [Yaml](https://en.wikipedia.org/wiki/YAML) config file.

## Other examples

Generally the file format can be desumed from file name / extension.
However one can enforce Yaml format when it can't be detected automatically

```bash
wp salts generate --format=yaml --my.config
```

One could also append want to append some custom WordPress salts, such as `WP_CACHE_KEY_SALT` used by [Memcached Object Cache](https://github.com/Automattic/wp-memcached/blob/master/object-cache.php), [WP Redis](https://github.com/pantheon-systems/wp-redis/blob/c99e1e850ab28453d0fceac7d621dfbad94d1f0b/object-cache.php) and other plugins:

```bash
wp salts generate --format=env --append=WP_CACHE_KEY_SALT,OTHER_USEFUL_SALT

AUTH_KEY='|p/>=h19^l/tFT=_e yOu+_@Zs?VMUObx@@Gx2SD/rPFdb(?jBbpE~w+l-[<ie,o'
SECURE_AUTH_KEY='*G|+h:%xMCY.ALkJ)72/0Y=0^5A^}PS:~xb7-:Y+_##D|sg*uU{o6(w<h>,g|2`o'
LOGGED_IN_KEY='A>r0rY]%#x1CvPqzFN[ (pTRNt1|p[RiFooj<s&w)%<+tI#z/x8Xos%a>_C ]-w>'
NONCE_KEY='SoBdx~Nh+FXhniyc7?oTY;|rjvLsTa!yV:Z-_z*Bs)`6z53ld^B4vVdqVJ&ass/u'
AUTH_SALT='hcSd}*.rYZ9g^<1wYGhs_xv<soGS/$} 4*#rsT?Nh!o$elz394!+I5>LPl)AmfLL'
SECURE_AUTH_SALT='bg>!x{vYz<N|h}uxcE5fMl8$5l$0yJ#t3c<z#q4F7]UU0OUJ75(jd1MB2}A(c9r`'
LOGGED_IN_SALT='5gsbcBPv;M`Ii)c}fZ{>D$4n0=|_5Lu#+)UNw`L`dF$w/`uP{&*xdI9&[`j^Q*3<'
NONCE_SALT='S07L}$OO*<R$)hWtawhST{Uqe!ZE0M3!`W-gz6]fY4y9joX9o;]+sS[O$_`)JiiO'
# >>> Note these two additional WordPress salts below
WP_CACHE_KEY_SALT='dzwdcllV*h/@BG/Hh~@tRM_!aV~d.55/o(eoC= E&LBJjG{V1~xl?nN<DJ>jobsp'
OTHER_USEFUL_SALT='hrRt.Ldx&=ywXCCU;5,sOkq ZaAUz7vz3lMp?~,L.EcgpdJ<c_T$4GnAYtKkVhO}'
```
