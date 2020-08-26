# ROUTES

Routes may be defined like this:

```
$phpht->router->[verb]("[URI_path_regex]",[function]);
```

Where **VERB** is the HTTP verbs: *get*, *post*, *put*, and *delete*.

The **URI_path_regex** is a regular expression that describes the URI that, when detected, will trigger the *functon*.

The **function** is a PHP callable that accepts arguments detected in the *URI_path_regex* and starts the chain.

## VERBS

The functions that are triggered are triggered based on the combination of the HTTP verb that was used to call the URI. 

You specify the verb like this:

```
$phpht->router->verb()
```

Verbs are specified in lower-case. Here are some examples:

 - $phpht->router->get("[URI_path_regex]",[function])
 - $phpht->router->post("[URI_path_regex]",[function])
 - $phpht->router->put("[URI_path_regex]",[function])

When the combination of *verb* and *URI path* is matched, the function will be called with any matched elements passed.

## URI Path Regex

The **URI_path_regex** is a regular expression that matches a URI path.

You may use all the typical regex techniques to specify the path like anchors (^,$), character classes, quantifiers (+,?,*), and captures.

Captures deserve special attention. They allow us to collect parts of the URI to be fed into the attached function. Any text captured gets fed into the function in a PHP **matches** array.

The **matches** array has the form:

 - **$matches[0]**: the text that matched the full pattern
 - **$matches[1]**: text that matched the first capture
 - **$matches[2]**: second capture...
 - . . .

The regex tries to match the URI. Here are some examples of regexes matching URI's:

### Match [your_site]/_info/

This will match a URI such as ```https://[my_site]/_info``` or ```https://[my_site]/_info/```
 
```
/^\\/_info\\/?/
``` 

The URI path regex does not include the root elements of the URI. So, in GET https://my_site/_info/ (from above), the **my_site** part of the URI does not need to be defined in the regex.

Note how we use the **?** quantifier to specify *zero* or *one* ending slashes.

Also note how we use slashes to define the regex. We could use any character to define the regex (as allowed by PHP).

### Match and Capture a Student ID

Imagine you want to match a URI that specifies the ID of a student. You could do it like this:

```
/^\\/(students)\\/([0-9]+)\\/?/
```

So, if this was requested: ```https://[my_site]/students/12345/``` the above regex would capture it, trigger the attached function, and pass the following to the function in a **$matches** array like this:

```
match[0] => /students/1234/
match[1] => students
match[2] => 1234
```

## The Function

Once a URI and any matches have been captured, a **function** is invoked and the *$matches*  array is passed to the function.

Functions can be specified like this:

```
$phpht->router->get("/^\\/students\\/?/","listStudents");
```

Like this:

```
$phpht->router->get("/^\\/students\\/?/",function($matches) {
  .
  .
  listStudents($matches);
  .
  .
});
```

If you want the context of an object you can do this:

```
$phpht->router->get("/^\\/students\\/?/",function($matches) use ($my_controller) {
  .
  .
  $my_controller->listStudents($matches);
  .
  .
});
```

Or, like this:

```
$phpht->router->get(
  "/^\\/students\\/?/",
  array($my_controller,"listStudents")
);
```

We tend to use the above one the most as it keeps the *routes* file cleaner looking. 
