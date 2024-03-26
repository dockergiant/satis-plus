# dockergiant/satis-plus

## Usage

### 1) Create SATIS project

```bash
git clone https://github.com/dockergiant/satis-plus
cd satis-plus
# PHP 8.1
composer install

bin/satis-plus
```


### 2) Generate Gitlab/Github SATIS configuration

```bash
# add --archive if you want to mirror tar archives
bin/satis-plus gitlab-to-config \
    --homepage https://satis.example.org \
    --output satis.json \
    --archive \
    https://gitlab.example.org [GitlabToken]
```

### 3) Use SATIS as usual

```bash
bin/satis-plus build satis.json web
```

