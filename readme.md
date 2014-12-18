## Laravel - how to define and use Eloquent Global Scopes

Learn how to use Laravel's 4.2 `GlobalScopes`. More info [softonsofa.com/laravel-how-to-define-and-use-eloquent-global-scopes](http://softonsofa.com/laravel-how-to-define-and-use-eloquent-global-scopes/)


### Usage

1. clone the repo with `git clone https://github.com/jarektkaczyk/laravel4-global-scope-example.git`
2. run `composer install` in order to pull all the packages
3. migrate the db (sqlite = no setup required) with `php artisan migrate`
4. seed the db with `php artisan db:seed`
5. run the REPL with `php artisan tinker` and play with the `Post` model:

```
[1] > Post::toSql();
// default behaviour - scope applied
// 'select * from "posts" where "posts"."published" = ?'

[2] > Post::withDrafts()->toSql();
// scope removed using static method on the trait
// 'select * from "posts"'

[3] > Post::query()->withDrafts()->toSql();
// scope removed using macro on the Eloquent\Builder
// 'select * from "posts"'
[4] > 
```
