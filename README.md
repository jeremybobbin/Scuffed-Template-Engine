# Scuffed-Template-Engine
A simple contemplating engine object written in PHP that renders dynamic HTML using passed in strings or arrays.

Reads statements placed within curely brackets.

This is how to use it:
  1. Declare instance of the engine
    
    $directory = __DIR__ . '\\template_folder';
    $te = new ScuffedTemplatingEngine('html', $directory);

  2. Write HTML
  
    <h1>{username}</h1>
    <ol>{foreach(tasks)}</ol>
    
  3. Write variables
    
    $values = [
      'username' => 'jeremybobbin',
      'tasks' => $taskList
    ]; 
    
    $taskList = [
      '<ul>Do laundry</ul>',
      '<ul>Write this</ul>',
      '<ul>Get money</ul>',
      '<ul>Shit post</ul>',
    ];
  
  4. Render
  
    echo $te->render('templateFile', $values);
    

{if(string)} - Only renders if parameter is set.
{foreach(array)} - Renders an array of strings.
{include(file)} - Includes html file.
