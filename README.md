# Eloquent logger

Logger of modifications of eloquent models. Saves:

* **table**
* **table_id**
* **operation** (created, updated, deleted)
* **user_id** (if authorized)
* **modified data**

### Example

Database table

```sql
create table article (
    id bigserial,
    title text,
    description text,
    content text
);
```

Model

```php
<?php

use Illuminate\Database\Eloquent\Model;
use Demidovich\EloquentLogger\Logger;

class Article extends Model
{
    use HasEloquentLogger;
}
```

Create records

```php
<?php

$article = new Article();
$article->title = "Title A";
$article->description = "Description A";
$article->content = "Content A";
$article->save();

$article->title = "Title B";
$article->save();

$article->delete();
```

Results

```sql
select * from modification_log;

-[ RECORD 1 ]--+-------------------------------------------------------------------------------
id             | 1
operation      | created
table          | article
table_id       | 1
user_id        | 
modified_state | {"id":1,"title":"Title A","description":"Description A","content":"Content A"}
created_at     | 2024-04-08 07:21:42

-[ RECORD 2 ]--+-------------------------------------------------------------------------------
id             | 2
operation      | updated
table          | article
table_id       | 1
user_id        | 
modified_state | {"id":1,"title":"Title B"}
created_at     | 2024-04-08 07:21:42

-[ RECORD 3 ]--+-------------------------------------------------------------------------------
id             | 3
operation      | deleted
table          | article
table_id       | 1
user_id        | 
modified_state | {}
created_at     | 2024-04-08 07:21:42
```
