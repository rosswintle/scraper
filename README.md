## Scraper

This PHP script is a scraper aid that runs multiple Regular Expression patterns over multiple files to extract variables into JSON.

### Installing

You'll need a small PHP library to run this so, after cloning, you'll need to run:

```
> composer install
```

to pull in the dependencies.  The reason for this is that I partly wanted to try out some of the methods in Adam Wathan's book "Refactoring to Collections", so I use Laravel's Collection class a little.

### Running

You run the script like this:

```
> php scraper.php filelist.txt patternfile.php > output.json
```

### Inputs

The filelist is a simple text file with one file path/name on each line

The pattern file is a php script that returns an array. For example:

```
<?php
  return [
     'date' => '<span class="date-published">([^<]*)<\/span>',
     'title' => '<h2 class="post-title">\s*(.*)\s*<\/h2>',
     'content' => '<div class="post-content content-body">(.*)<\/div>',
   ];
?>
```

### Outputs

The script echos a JSON version of an array like:

```
{
 'file1.html': {
    'data' : {
      'date' : <scraped date data>,
      'title' : <scraped title data>,
      'content' : <scraped content data>
    }
  },
  'file2.html' : {
    'data' : {
      'date' : <scraped date data>,
      'title' : <scraped title data>,
      'content' : <scraped content data>
    }
  }
}
```
