/*
Редактор задач (Task Editor, Редактор языка определения задач) - компонент,
предназначенный для интерактивного набора документов описания задач
в формате JSON для Системы Управления Выполнением Заданий Pilot. Он позволяет
ускорить и упростить набор таких документов, а также осуществляет проверку
корректности их ввода по ряду параметров.

Компонент написан на зыке JavaScript и состоит из двух файлов:
task_editor.js и task_editor.conf. Первый из них реализует функциональность
компонента, второй содержит определение его конфигурации, в частности
схему описания задач для системы Pilot. Кроме того, в task_editor.conf
создаётся глобальный объект task_editor, который фактически
содержит все элементы программы.

Первым должен быть подключён файл task_editor.conf, вторым task_editor.js:

    <script type="text/javascript" src="task_editor.conf"></script>         
    <script type="text/javascript" src="task_editor.js"></script>

API компонента представлен классом task_editor.TaskEditor. Для того чтобы
задействовать его во внешнем приложении, нужно иметь в наличии ссылку
на пустой div-элемент, в котором будет происходить визуализация компонента:

            // div - ссылка на пустой div-элемент
            var editor = new task_editor.TaskEditor(div);                       
            editor.run();

Вызов функции run() осуществляет визуализацию редактора в окне браузера.

Другие методы API компонента:

load(task_string)  - загружает строку task_string с JSON-описанием задачи
                     в компонент.

text() - возвращает строку с описанием задачи, отвечающую текущему
         состоянию текстовых полей и параметров по умолчанию, заданных
         в конфигурационном файле task_editor.conf. Если для какого-либо
         параметра не указано значение в текстовом поле, либо текстовое поле
         вовсе отсутствует, то берётся значение по умолчанию, указанное
         в этом файле, если таковое там имеется.

clear() - очищает текстовые поля редактора.

Кроме перечисленных методов компонент имеет булево свойство changed,
которое инициализируется значением false. Оно становится true,
если пользователь изменяет содержимое текстовых полей редактора.
Внешнее приложение может при необходимости присвоить ему значение false.

Код компонента разделён на модули class.js, utils.js, gui.js, private_gui.js,
task_editor.js, выполняющие самостоятельные функции в программе.
*/





/*
Uses
    task_editor.conf;
*/


		//
		// parsers.js
		//
		
		// Некоторые функции для работы со строками
		


task_editor.deleteBorderGaps = function (str) {
    // Удаляет граничные пробелы и символы перехода на следующую строку в str
    
    var i = 0; 
    var j = str.length - 1;	 
    var c = str.charAt(i);
    
    if ( (navigator.appName == "Opera") || (navigator.appName == "Microsoft Internet Explorer") ) {
    
            if (c == "\n") {
                i++;  
            }            
        
            while (i < str.length) {       
                c = str.charAt(i);
                var c1 = str.charAt(i+1);         // => ( симв. возврата каретки , симв. перехода на след. строку ) 
            
                if (c == " ") {                
                    i++;
                }
                else if (c1 == "\n") {
                    i += 2;
                }
                else {
                    break;
                }        
            }
                
            while (j >= 0) {       
                c = str.charAt(j);
                                
                if (c == " ") {                
                    j--;
                }
                else if (c == "\n") {
                    j -= 2;
                }
                else {
                    break;
                }        
            }        
    }
    else {
            while ( ( (c == ' ') || (c == '\n') ) && (i < str.length) ) {
                i++;
                c = str.charAt(i);
            }

            c = str.charAt(j);
            while ( ( (c == ' ') || (c == '\n') ) && (j >= 0) ) {
                j--;
                c = str.charAt(j);
            }
    }   
               
    str = str.substring(i, j+1);
    return str;
}



task_editor.processQuotes = function (s) {
    //  Заменяет '"' на '\"'
    
    var s1 = "";
    
    for (var j = 0; j < s.length; j++) {
        c = s.charAt(j); 
        if (c != '\"') {
            s1 += c;                     
        }
        else {
            s1 +="\\\"";
        }
    } 
    
    return s1;
}
 
 



		//
		// class.js
		//
                
                // Надстройка над прототипно-ориентированным JavasScript'ом для работы с классами 

/*
Uses
    parsers.js;
*/ 


task_editor.declareClass = function (className, props) {
    /*     
       Объявляет класс className со свойствами props 
       и реализует его как некоторую функцию.
       
       Пример: 
    
       Класс текстового поля
      
           task_editor.declareClass("task_editor.tEdit", {
               constructor: function(size, text) {                               
                         this.input = document.createElement("input");
                         this.input.type = "text";
                         this.input.size = size;
                         if (text) {
                             this.input.value = text;
                         }               
               },
               getDOMobject: function() {	           	                                  
                         return this.input;    	         
               }
           });          
    */
       
    var cbs = task_editor.getClassBodyString(props);                 
    var as = task_editor.getArgumentsString(cbs);                      
    var ccs = task_editor.getConstructorContentString(cbs);                                                    
    var declare_s = className + " = function(" + as + ") {\n" + cbs + ccs + "\n};"; 
    
    eval(declare_s);                    
}


task_editor.getClassBodyString = function (props) {
    // Возвращает строку с элементами вида 
    // "this." + key + " = " + props[key];\n"
    // для всех свойств key объекта props

    var cbs = "";
    var i = 0;       
    var N = task_editor.getPropsNumber(props);   
       
    if ( navigator.appName == "Microsoft Internet Explorer" ) {
            if (props.constructor) {
                cbs += "this.constructor = " + props.constructor + ";\n";                             
            }       
    } 
            
    for(key in props) if (props.hasOwnProperty(key)) { 
        cbs += "this." + key + " = " + props[key];              
        if (i < N - 1) {
            cbs += ";\n";  
        }              
        i++;  
    }
            
    return cbs;
}
     
        
task_editor.getArgumentsString = function (cbs) {
    // Возвращает строку с аргументами конструктора класса

    var i = 27;   
    var c = cbs.charAt(i);
    var temps = "";
    
    while (c != '(') { 
        temps += c;
        c = cbs.charAt(i);
        i++;          
    }
      
    if ( (navigator.appName == "Opera") || (navigator.appName == "Microsoft Internet Explorer") ) {
        i++;
    }           
    
    c = cbs.charAt(i);
    
    var j = i;    
    temps = "";                                   
    while (c != ')') {
        j++;          
        temps += c;            
        c = cbs.charAt(j);          
    }
            
    var as = cbs.substr(i, j-i);       
    return as;              
}       
        
        
task_editor.getConstructorContentString = function (cbs) {
    // Возвращает строку с содержимым конструктора

    var i = 30;
    var c = cbs.charAt(i);    
    var temps = "";
            
    while (c != '{') {
        i++;       
        c = cbs.charAt(i);        
        temps += c;            
    } 
            
    i++;                                      
    var j = i;    
    c = cbs.charAt(j);
    var c1 = cbs.charAt(j+1);
        
    while (true) {    
         c = cbs.charAt(j);
         
         if (c == '}') {
             c1 = cbs.charAt(j+1);
             if (c1 == ';') {
                 var tests = cbs.substr(j+2);
                 if (task_editor.nextThis(tests)) {
                     break;                 
                 }                         
             }
         }
         
         j++;
    }
      
    var ccs = cbs.substr(i, j-i);       
    return ccs;              
}


task_editor.getPropsNumber = function (obj) { 
    // Возвращает число свойств объекта obj, не входящих в его прототип

    var count = 0;     
    for(key in obj) if (obj.hasOwnProperty(key)) {
        count++;   
    }
    
    return count;
}


task_editor.nextThis = function (s) {
    // Возвращает true, если строка s начинается на this, в противном случае false

    s = task_editor.deleteBorderGaps(s);
    var yes = false;
    var tests = s.substr(0, 4);
      
    if (tests == "this") {
        yes = true;    
    }
    
    return yes; 
} 





		//
		// utils.js
		//
                
                // Различные вспомогательные функции 


// Работа с DOM

task_editor.hasChild = function (node, child) {
    // Возвращает true, если child является дочерним DOM-элементом node

    var children = node.childNodes;
    var has = false; 
    for (var i = 0; i < children.length; i++) {
        if (children[i] == child) {
            has = true;
            break;
        }
    } 
    return has;
}



task_editor.removeArrayOfChildren = function (container, array_of_children) { 
    // Удаляет из node дочерние элементы из массива array_of_children

    for (var i = 0; i < array_of_children.length; i++) {
        container.removeChild(array_of_children[i]);
    }     
}



task_editor.removeChildSafely = function (node, child) {
    // Удаляет child из DOM-элемента node.
    // Если операция удаления прошла успешно, возвращает true,
    // в противном случае false

    var removed = false;
    if (task_editor.hasChild(node, child)) {
        node.removeChild(child);
        removed = true;    
    }
    return removed;
}



// Функции для работы с массивами и объектами

task_editor.objGotProperty = function (obj, property_string) {
    // Возвращает true, если объект obj имеет свойство property_string,
    // в противном случае false 

    var got = false;
    if (obj && (obj[property_string] != undefined) ) {
        got = true;    
    }
    return got;
}



task_editor.arrayGotElement = function (array, elem) {
    // Возвращает true, если массив array содержит элемент elem,
    // в противном случае false 
    
    var got = false;
    for (var i = 0; i < array.length; i++) {
        if (array[i] == elem) {
            got = true;
        }        
    }
    return got;
}



task_editor.copyArray = function(arr) {
    // Возвращает копию массива arr

    var copy = [];
    
    for (var i = 0; i < arr.length; i++) {
        var type = task_editor.get_type(arr[i]);
    
        if (type == "object") {
            copy[i] = task_editor.copyObject(arr[i]);
        }
        else if ((type == "array")) {
            copy[i] = task_editor.copyArray(arr[i]);
        }
        else {
            copy[i] = arr[i];
        }                            
    }
    
    return copy;
}



task_editor.copyObject = function(obj) {
    // Возвращает копию объекта obj

    var copy = {};
    
    for(key in obj) if (obj.hasOwnProperty(key)) {
        var type = task_editor.get_type(obj[key]);
        
        if (type == "object") {
            copy[key] = task_editor.copyObject(obj[key]);
        }
        else if ((type == "array")) {
            copy[key] = task_editor.copyArray(obj[key]);
        }
        else {
            copy[key] = obj[key];
        }               
    }
    
    return copy;
}


task_editor.getEventObject = function(array, event_generator, property_string) {
/*
   Возвращает ссылку на объект из массива объектов, в котором возникло
   некоторое событие.
   
   array - массив объектов;   
   event_generator - DOM-элемент, в котором произошло событие   
   property_string - строка с именем свойства объекта, 
                     которое должно содержать ссылку на этот DOM-элемент 

*/

    var i; 
    for (i = 0; i < array.length; i++) {            
        if (event_generator == array[i][property_string]) {
            break;           
        }
    }
    var event_object = array[i];
    return event_object;
}
 

// Работа со строками

task_editor.getEmptyString = function (length) {
    // Возвращает строку из length пробелов

    var s = "";
    for (var i = 0; i < length; i++) {
        s += ' ';  
    }
    return s;
}

task_editor.gotQuote = function (string) {
    // Возвращает true, если строка string содержит кавычки,
    // в противном случае false 

    var got = true;

    var i1 = string.indexOf("\"");
    var i2 = string.indexOf("'"); 

    if ( (i1 == -1) && (i2 == -1 ) ) {
        got = false;
    }

    return got;
}



task_editor.removeQuotes = function (string) {
    // Удаляет кавычки из строки string 

    var s1 = "";
    var s2 = string;
    
    do {
        s1 = s2;
        s2 = s1.replace("\"", "");
    } while (s1 != s2)
    
    do {
        s1 = s2;
        s2 = s1.replace("'", "");
    } while (s1 != s2)    

    return s1;   
}



task_editor.appendSlashBeforeQuotes = function (string) {
    // Добавляет символ \ перед каждой двойной кавычкой

    var s = "";

    for (var i = 0; i < string.length; i++) {
        var c = string.charAt(i);
        
        if (c == "\"") {
            s += "\\\"";               
        }
        else {
            s += c;
        }
    }
      
    return s;  
}



// Прочие функции

task_editor.get_type = function (x) {
    // Возвращает строку с типом переменной x

    var type = typeof(x);
    if ( (type == "object") && (x.slice) && (x.splice) ) {
        type = "array"; 
    }        
    return type;
}





		//
		// gui.js
		//
		
		// Примитивные компоненты GUI. В основном классы DOM-элементов.
		// Метод getDOMobject() возвращает ссылку на соответствующий 
                // DOM-элемент.
                
 /*
Uses
    class.js;          
*/ 


task_editor.declareClass("task_editor.tDiv", {
    constructor: function(id, classname) {    
              this.id = id;
              this.className = classname;
    },
    getDOMobject: function() {
              var div = document.createElement("div");		           	        
              div.id = this.id;
              div.className = this.className;
              return div;    	         
    }
});



// Текстовая метка

task_editor.declareClass("task_editor.tLabel", {    
    constructor: function(text, desc) {     
              this.text = text + ' ';
              this.desc = desc;
    },
    getDOMobject: function() {		           	        
              var span = document.createElement("span");
              span.innerHTML = this.text;
              span.className = "label";
              span.title = this.desc;
              return span;    	         
    }
});



// Текстовое поле

task_editor.declareClass("task_editor.tEdit", {
    constructor: function(size, text) {                               
              this.input = document.createElement("input");
              this.input.type = "text";
              this.input.size = size;
              if (text) {
                  this.input.value = text;
              }               
    },
    getDOMobject: function() {	           	                                  
              return this.input;    	         
    }
});

// Поле textarea
//

task_editor.declareClass("task_editor.tMemo", {
    constructor: function(cols, rows, text) {                               
              this.memo = document.createElement("textarea");
              this.memo.cols = cols;
              this.memo.rows = rows;
              if (text) {
                  this.memo.value = text;
              }               
    },
    getDOMobject: function() {	           	                                  
              return this.memo;    	         
    }
});


task_editor.declareClass("task_editor.tButton", {
    constructor: function(text, handler) {     
              this.text = text;
              this.handler = handler;
    },
    getDOMobject: function() {		        
              var button = document.createElement("button");
              button.innerHTML = this.text;
                           
              if (this.handler) {              
                  button.onclick = this.handler;
              }
                            
              return button;          	         
    }
});



task_editor.declareClass("task_editor.tSelect", {
    constructor: function(options, values) { 
              // options is an array of options text
              // values is an array of value attribute for each option 
              
              this.values = values; 
                                              
              this.select = document.createElement("select");
              
              for (i = 0; i < options.length; i++) {
                  var option = document.createElement("option");
                  option.innerHTML = options[i]; 
                  option.value = values[i];
                  this.select.appendChild(option);
              }
    },
    getDOMobject: function() {		            
              return this.select;          	         
    },
    getValue: function() {
              var index = this.select.selectedIndex;              
              return this.values[index];     
    },
    setValue: function(value) {
              try {

                  var result = false;
                  
                  var i;
                  for (i = 0; i < this.values.length; i++) {
                      if (this.values[i] == value) {
                          result = true;
                          break;
                      }                  
                  }

                  if (!result) {
                      throw -1;                  
                  }
                  
                  this.select.selectedIndex = i;

                  return true;
              
              } catch (e) {
                  return false;
              } 
              
    }
}); 





		//
		// private_gui.js
		//
		
		/*
                   Классы компонентов GUI, предназначенные для визуализации
                   свойств "properties" в JSON-схеме описания задач
                   в соответствии с их типами.
                   
                   Для краткости описание аналогичных по назначению элементов
                   приводится один раз.                    
                */    
                
 /*
Uses
    class.js, utils.js, gui.js;          
*/ 

// task_editor.StringGUI

// Визуализирует тип "string", создавая div-элемент с одним текстовым полем

task_editor.string_gui = {};  // Служит для введения специального
                              // пространства имён для класса
                              // task_editor.StringGUI

task_editor.declareClass("task_editor.StringGUI", {
    constructor: function(label, size, height_parameter, value, changable_obj, desc) {
              /*
                 label - текстовая метка перед полем ввода;
                 size - размер текстового поля;
                 height_parameter - положительный целочисленный параметр, регулирующий
                                    высоту div-элемента с данным компонентом;
                 value - значение текстового поля.
                 
                 Объект changable_obj должен иметь булево свойство changed.
                 Изменение содержимого текстового поля объекта task_editor.StringGUI
                 приводит к присвоению этому свойству значения true.                                  

                 desc - текст всплывающей подсказки

              */               
              
              this.type = "string";
                                         
              var dom_label = (new task_editor.tLabel(label, desc)).getDOMobject();
              this.text_field = (new task_editor.tEdit(size, value)).getDOMobject();
              this.main_div = (new task_editor.tDiv()).getDOMobject();
              
              this.main_div.style.height = height_parameter + "px";
              
              this.main_div.appendChild(dom_label);
              this.main_div.appendChild(this.text_field);
                            
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.string_gui.this1 = this;
                  this.changable_obj = changable_obj;              
                  this.text_field.onchange = task_editor.string_gui.onTextFieldChange; 
              }                    
    },
    getDOMobject: function() {
              // Возвращает ссылку на div-элемент с данным компонентом. 
              return this.main_div;		           	        	         
    },
    loadString: function(value) {
              // Загружает строку value в текстовое поле объекта    
              this.text_field.value = value;
    },
    getString: function() {
              // Возвращает строку с содержимым текстового поля    
              return this.text_field.value;
    },
    clear : function() { 
              // Очищает текстовое поле объекта                                            
              this.text_field.value = "";     
    }       
});

task_editor.string_gui.onTextFieldChange = function() {
    // Используется для установки в true свойства changed объекта changable_obj
    // при изменении текстового поля
    task_editor.string_gui.this1.changable_obj.changed = true;
}

//------------------------------------------------------------------------------

// task_editor.MemoGUI

// Аналогичен task_editor.StringGUI. Предназначен для работы с 
// типом "memo", который представляет собой JSON объект с произвольным набором атрибутов

task_editor.memo_gui = {};

task_editor.memo_gui.objects = []; 

task_editor.declareClass("task_editor.MemoGUI", {
    constructor: function(label, cols, rows, height_parameter, value, offset, changable_obj, desc) {

              this.type = "memo";

              task_editor.memo_gui.objects.push(this);

              var dom_heading = (new task_editor.tLabel(label, desc)).getDOMobject();
              this.edit_button = (new task_editor.tButton(task_editor.config.buttons.edit.value, task_editor.memo_gui.edit_handler)).getDOMobject();

              var top_div = (new task_editor.tDiv()).getDOMobject();             
              top_div.appendChild(dom_heading); 
              top_div.appendChild(this.edit_button);
              top_div.style.height = height_parameter + "px";  


              this.inner_div = (new task_editor.tDiv()).getDOMobject();    
              this.inner_div.style.position = "relative"; 
              this.inner_div.style.paddingLeft = offset + "px";
              this.inner_div.style.display = "none";                                   

              this.memo = (new task_editor.tMemo(cols, rows, value)).getDOMobject();
              this.inner_div.appendChild(this.memo);                                   

              this.main_div = (new task_editor.tDiv()).getDOMobject();
              this.main_div.appendChild(top_div);           
              this.main_div.appendChild(this.inner_div);           
              
              this.opened = false;
                           
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.memo_gui.this1 = this;
                  this.changable_obj = changable_obj;               
                  this.memo.onchange = task_editor.memo_gui.onTextFieldChange; 
              }                    
    },
    getDOMobject: function() {
              return this.main_div;		           	        	         
    },
    loadMemo: function(memo) {
              this.clear();
              var JSONstr = JSON.stringify(memo, null, '\t');
              if (JSONstr) {
		JSONstr = JSONstr.substr(2, JSONstr.length - 4);
		JSONstr = JSONstr.replace(/^\t/mg,'');
              }
              this.memo.value = JSONstr;
    },
    getMemo: function() {
              return JSON.parse("{ " + this.memo.value + " }");
    },
    clear : function() {                          
              this.memo.value = "";     
              if (this.opened) {
                  this.close();   
              }                
    },       
    open: function() {
              // Показывает содержимое объекта в окне браузера

              this.inner_div.style.display = "block";                                   
              this.edit_button.innerHTML = task_editor.config.buttons.hide.value;    
              this.edit_button.onclick = task_editor.memo_gui.hide_handler;
              this.opened = true;
    },
    close: function() {
              // Скрывает содержимое объекта 

              this.inner_div.style.display = "none";                                   
              this.edit_button.innerHTML = task_editor.config.buttons.edit.value;    
              this.edit_button.onclick = task_editor.memo_gui.edit_handler; 
              this.opened = false;
    }             
});

task_editor.memo_gui.onTextFieldChange = function() {
    task_editor.memo_gui.this1.changable_obj.changed = true;
}


task_editor.memo_gui.edit_handler = function() {
    var this1 = task_editor.getEventObject(task_editor.memo_gui.objects, this, "edit_button");                           
    this1.open();             
}

task_editor.memo_gui.hide_handler = function() {
    var this1 = task_editor.getEventObject(task_editor.memo_gui.objects, this, "edit_button");                           
    this1.close();             

}

//------------------------------------------------------------------------------

// task_editor.IntegerGUI

// Аналогичен task_editor.StringGUI. Предназначен для работы с 
// типом "integer"

task_editor.integer_gui = {};

task_editor.declareClass("task_editor.IntegerGUI", {
    constructor: function(label, size, height_parameter, value, changable_obj, desc) {
              this.type = "integer";
                                         
              var dom_label = (new task_editor.tLabel(label, desc)).getDOMobject();
              this.text_field = (new task_editor.tEdit(size, value)).getDOMobject();
              this.main_div = (new task_editor.tDiv()).getDOMobject();
              
              this.main_div.style.height = height_parameter + "px";
              
              this.main_div.appendChild(dom_label);
              this.main_div.appendChild(this.text_field);
                            
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.integer_gui.this1 = this;
                  this.changable_obj = changable_obj;               
                  this.text_field.onchange = task_editor.integer_gui.onTextFieldChange; 
              }                    
    },
    getDOMobject: function() {
              return this.main_div;		           	        	         
    },
    loadInteger: function(value) {
              this.text_field.value = value;
    },
    getInteger: function() {
              var integer = Number(this.text_field.value);
              return integer;
    },
    clear : function() {                          
              this.text_field.value = "";     
    }       
});

task_editor.integer_gui.onTextFieldChange = function() {
    task_editor.integer_gui.this1.changable_obj.changed = true;
}



//------------------------------------------------------------------------------

// task_editor.BooleanGUI

// Визуализирует тип "boolean", создавая div-элемент с раскрывающимся списком

task_editor.boolean_gui = {};

task_editor.declareClass("task_editor.BooleanGUI", {
    constructor: function(label, height_parameter, changable_obj, desc) {
              this.type = "boolean";
                                         
              var dom_label = (new task_editor.tLabel(label, desc)).getDOMobject();
                                       
              this.select = new task_editor.tSelect(task_editor.config.properties.visualization.type.boolean.options, ["none", "true", "false"]);

              var select_elem = this.select.getDOMobject();
              
              this.main_div = (new task_editor.tDiv()).getDOMobject();
              
              this.main_div.style.height = height_parameter + "px";
              
              this.main_div.appendChild(dom_label);
              this.main_div.appendChild(select_elem);
                            
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.boolean_gui.this1 = this;
                  this.changable_obj = changable_obj;               
                  select_elem.onchange = task_editor.boolean_gui.onSelectChange;
              }                    
    },
    getDOMobject: function() {
              return this.main_div;		           	        	         
    },
    loadBoolean: function(boolean_) {
              this.select.setValue(String(boolean_));
    },
    getBoolean: function() {
              var boolean_string = this.select.getValue();
              var boolean_ = boolean_string;
              
              if (boolean_ != "none") { 
                  eval("boolean_ = " + boolean_string); 
              }             
              
              return boolean_;
    },
    clear: function() {                          
              this.select.setValue("none");
    }           
});

task_editor.boolean_gui.onSelectChange = function() {
    task_editor.boolean_gui.this1.changable_obj.changed = true;
}

 

//------------------------------------------------------------------------------

// task_editor.ArrayGUI

/*
   Визуализирует тип "array", создавая div-элемент с переменным набором
   текстовых полей  
*/ 

task_editor.array_gui = {};

task_editor.array_gui.objects = []; /* 
                                       Служит для хранения всех созданных
                                       объектов класса task_editor.ArrayGUI.                                       
                                       Это нужно для организации работы с 
                                       событиями нажатия на кнопку
                                       (класс task_editor.tButton). 
                                    */  

task_editor.declareClass("task_editor.ArrayGUI", {
    constructor: function(heading, size, height_parameter, changable_obj, desc) {
              /*
                 heading - заголовок элемента;
                 size - размер текстовых полей;
                 elem_name - добавляет дополнительные данные
                             в текст элементов-кнопок 
                             (фактически не используется).                                                                          
              */
    
              this.type = "array";
    
              this.size = size;
              this.height_parameter = height_parameter;
              
              task_editor.array_gui.objects.push(this);
    
              var dom_heading = (new task_editor.tLabel(heading, desc)).getDOMobject();

              this.inner_div = (new task_editor.tDiv()).getDOMobject();
              this.elem_divs = [];
              this.text_fields = []; 
              
              this.add_button = (new task_editor.tButton(task_editor.config.buttons.add.value, task_editor.array_gui.add_handler)).getDOMobject();
              this.remove_buttons = [];
              
              var top_div = (new task_editor.tDiv()).getDOMobject();             
              top_div.appendChild(dom_heading); 
              top_div.appendChild(this.add_button);
              top_div.style.height = height_parameter + "px";             
              
              this.main_div = (new task_editor.tDiv()).getDOMobject();
              this.main_div.appendChild(top_div);
              this.main_div.appendChild(this.inner_div);  
              
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.array_gui.this1 = this;
                  this.changable_obj = changable_obj;   
              }                                  
    },
    getDOMobject: function() {
              return this.main_div;		           	        	         
    },
    loadArray: function(array) {
              /*
                 Загружает массив array, что приводит к отображению его
                 в текстовых полях объекта. Этот массив должен содержать
                 только значения типа string или integer.                                  
              */   
              
              this.clear();
              for (i = 0; i < array.length; i++) {
                   task_editor.array_gui.add_elem(this, array[i]);             
              }
              task_editor.array_gui.rebuild_inner_div(this); 
    },    
    getArray: function() {
              // Возвращает массив значений текстовых полей объекта
              
              var array = [];
              for (i = 0; i < this.text_fields.length; i++) {
                   var elem = this.text_fields[i].value;
                   if (elem) {
                       array.push(elem);
                   }             
              }              
              return array;
    },
    clear: function() {
              // Очищает всё содержимое объекта. Визуально он возвращается 
              // в своё исходное состояние
    
              this.elem_divs = [];
              this.text_fields = [];     
              this.remove_buttons = [];
              task_editor.array_gui.rebuild_inner_div(this);
    }     
});


task_editor.array_gui.onTextFieldChange = function() {
    task_editor.array_gui.this1.changable_obj.changed = true;
}


task_editor.array_gui.getEventObjectIndexesIfPropertyIsArray = function(array, event_generator, property_string) {
/*
   Аналогична task_editor.array_gui.getEventObject, но 
   property_string здесь имя массива ссылок, одна из которых возможно
   ссылается на тот же объект, что и event_generator.
   
   Возвращает пару
   [ индекс_объекта_в_массиве_array, индекс_элемента_в_property_string]
   если обнаружено совпадение ссылки в массиве property_string с event_generator.
*/

    var i;
    var j;           
    for (i = 0; i < array.length; i++) {    
        var array2 = array[i][property_string];                
        var break_ = false;     
        for (j = 0; j < array2.length; j++) {       
            if (event_generator == array2[j]) {            
                break_ = true;                
                break;                                                                          
            }
        }        
        if (break_ ) {
            break;
        }
    }
    var indexes = [i, j];
    return indexes;
}


task_editor.array_gui.add_elem = function(this1, value) {
/*
   Добавляет в объект this1 класса task_editor.ArrayGUI новый элемент, 
   которому визуально соответствует текстовое поле с кнопкой для его удаления 
*/

    var elem_div = (new task_editor.tDiv()).getDOMobject();
    var elem_text_field = (new task_editor.tEdit(this1.size, value)).getDOMobject();     
    var remove_button = (new task_editor.tButton(task_editor.config.buttons.remove.value, task_editor.array_gui.remove_handler)).getDOMobject();

    elem_div.appendChild(elem_text_field);
    elem_text_field.onchange = task_editor.array_gui.onTextFieldChange;
    this1.text_fields.push(elem_text_field);
    
    var space_label = (new task_editor.tLabel(" ")).getDOMobject();
    elem_div.appendChild(space_label);
    
    elem_div.appendChild(remove_button);
    this1.remove_buttons.push(remove_button);
    
    elem_div.style.height = this1.height_parameter + "px";   
                            
    this1.inner_div.appendChild(elem_div);
    var index = this1.elem_divs.length;
    this1.elem_divs.push(elem_div);

    return index; 
}



task_editor.array_gui.add_handler = function() {
    // Обработчик события, вызываемый при нажатии на кнопку добавления элемента
      
    var this1 = task_editor.getEventObject(task_editor.array_gui.objects, this, "add_button");                           
    task_editor.array_gui.add_elem(this1);              
}

task_editor.array_gui.rebuild_inner_div = function(this1) {
    // Обновляет объект this1

    task_editor.removeChildSafely(this1.main_div, this1.inner_div)
    this1.inner_div = (new task_editor.tDiv()).getDOMobject();

    for (var i = 0; i < this1.elem_divs.length; i++) {
        this1.inner_div.appendChild(this1.elem_divs[i]);
    }
    
    this1.main_div.appendChild(this1.inner_div);
}

task_editor.array_gui.remove_handler = function() {
    // Обработчик события, вызываемый при нажатии на кнопку удаления элемента
    
    var indexes = task_editor.array_gui.getEventObjectIndexesIfPropertyIsArray(task_editor.array_gui.objects, this, "remove_buttons");
    var this1 = task_editor.array_gui.objects[indexes[0]];
    var index = indexes[1];
    
    if ( (this1.text_fields[index].value == "") || window.confirm(task_editor.config.confirm_window_massage) ) {
        this1.elem_divs.splice(index, 1);
        this1.remove_buttons.splice(index, 1);
        this1.text_fields.splice(index, 1);

        task_editor.array_gui.rebuild_inner_div(this1);
    }
}



//------------------------------------------------------------------------------

// task_editor.ObjectGUI

/* 
   Аналогичен task_editor.ArrayGUI. Визуализирует тип "object", 
   создавая div-элемент с переменным набором пар текстовых полей    
*/ 

task_editor.object_gui = {};

task_editor.object_gui.objects = [];

task_editor.declareClass("task_editor.ObjectGUI", {
    constructor: function(heading, size, height_parameter, comment1, comment2, changable_obj, desc) {
              //  comment1 text_field1 comment2 text_field2 - порядок отображения
              //  пары текстовых полей;  
              //  comment1 и comment2 - строки перед этими текстовыми полями 
              
              this.type = "object";
    
              this.size = size;
              this.height_parameter = height_parameter;
              
              this.comment1 = comment1;
              this.comment2 = comment2;
              
              task_editor.object_gui.objects.push(this);
    
              var dom_heading = (new task_editor.tLabel(heading, desc)).getDOMobject();
              
              this.inner_div = (new task_editor.tDiv()).getDOMobject();
              this.elem_divs = [];
              this.text_fields_pairs = []; 
              
              this.add_button = (new task_editor.tButton(task_editor.config.buttons.add.value, task_editor.object_gui.add_handler)).getDOMobject();

              this.remove_buttons = [];
              
              var top_div = (new task_editor.tDiv()).getDOMobject();             
              top_div.appendChild(dom_heading); 
              top_div.appendChild(this.add_button);
              top_div.style.height = height_parameter + "px";   
              
              this.main_div = (new task_editor.tDiv()).getDOMobject();
              this.main_div.appendChild(top_div);           
              this.main_div.appendChild(this.inner_div);  
              
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.object_gui.this1 = this;
                  this.changable_obj = changable_obj;   
              }                                  
    },
    getDOMobject: function() {
              return this.main_div;		           	        	         
    },
    loadObject: function(object) {
              /*
                 Загружает объект object, что приводит к отображению его
                 в текстовых полях объекта. Этот объект должен содержать
                 только значения типа string или integer.                                  
              */     
    
              this.clear();
                           
              for(var key in object) if (object.hasOwnProperty(key)) {
                  task_editor.object_gui.add_elem(this, key, object[key]); 
              }
              task_editor.object_gui.rebuild_inner_div(this); 
    },    
    getObject: function() {
              // Возвращает объект со значениями текстовых полей
    
              var object = {};
              for (i = 0; i < this.text_fields_pairs.length; i++) {
                   var value1 = this.text_fields_pairs[i][0].value;
                   var value2 = this.text_fields_pairs[i][1].value;
                   if (value1 || value2) {                       
                       object[value1] = value2;
                   }             
              }              
              return object;
    },
    clear: function() {
              // Очищает всё содержимое объекта. Визуально он возвращается 
              // в своё исходное состояние    
    
              this.elem_divs = [];
              this.text_fields_pairs = [];     
              this.remove_buttons = [];
              task_editor.object_gui.rebuild_inner_div(this);
    }     
});


// Назначение функций task_editor.object_gui.название_функции такое же
// как и в случае task_editor.ArrayGUI

task_editor.object_gui.onTextFieldChange = function() {
    task_editor.object_gui.this1.changable_obj.changed = true;
}

task_editor.object_gui.getEventObjectIndexesIfPropertyIsArray = function(object, event_generator, property_string) {
    var i;
    var j;           
    for (i = 0; i < object.length; i++) {    
        var object2 = object[i][property_string];                
        var break_ = false;     
        for (j = 0; j < object2.length; j++) {       
            if (event_generator == object2[j]) {            
                break_ = true;                
                break;                                                                          
            }
        }        
        if (break_ ) {
            break;
        }
    }
    var indexes = [i, j];
    return indexes;
}


task_editor.object_gui.add_elem = function(this1, value1, value2) {
    var elem_div = (new task_editor.tDiv()).getDOMobject();
    
    var elem_comment1 = (new task_editor.tLabel(this1.comment1)).getDOMobject(); 
    var elem_text_field1 = (new task_editor.tEdit(this1.size, value1)).getDOMobject();
    var space_label = (new task_editor.tLabel(" ")).getDOMobject();
    var elem_comment2 = (new task_editor.tLabel(this1.comment2)).getDOMobject();  
    var elem_text_field2 = (new task_editor.tEdit(this1.size, value2)).getDOMobject();   
    var elem_text_fields_pair = [elem_text_field1, elem_text_field2];
      
    var remove_button = (new task_editor.tButton(task_editor.config.buttons.remove.value, task_editor.object_gui.remove_handler)).getDOMobject();

    elem_div.appendChild(elem_comment1);
    elem_div.appendChild(elem_text_field1);
    elem_div.appendChild(space_label);
    elem_div.appendChild(elem_comment2);
    elem_div.appendChild(elem_text_field2);
    elem_text_field1.onchange = elem_text_field2.onchange = task_editor.object_gui.onTextFieldChange;    
    this1.text_fields_pairs.push(elem_text_fields_pair);
    
    var space_label = (new task_editor.tLabel(" ")).getDOMobject();
    elem_div.appendChild(space_label);
    
    elem_div.appendChild(remove_button);
    this1.remove_buttons.push(remove_button); 
    
    elem_div.style.height = this1.height_parameter + "px";  
                            
    this1.inner_div.appendChild(elem_div);
    var index = this1.elem_divs.length;
    this1.elem_divs.push(elem_div);
    
    return index; 
}



task_editor.object_gui.add_handler = function() {
    var this1 = task_editor.getEventObject(task_editor.object_gui.objects, this, "add_button");                           
    task_editor.object_gui.add_elem(this1);              
}

task_editor.object_gui.rebuild_inner_div = function(this1) {
    task_editor.removeChildSafely(this1.main_div, this1.inner_div)
    this1.inner_div = (new task_editor.tDiv()).getDOMobject();

    for (var i = 0; i < this1.elem_divs.length; i++) {
        this1.inner_div.appendChild(this1.elem_divs[i]);
    }
    
    this1.main_div.appendChild(this1.inner_div);
}

task_editor.object_gui.remove_handler = function() {    
    var indexes = task_editor.object_gui.getEventObjectIndexesIfPropertyIsArray(task_editor.object_gui.objects, this, "remove_buttons");
    var this1 = task_editor.object_gui.objects[indexes[0]];
    var index = indexes[1];
        
    var text_field1 = this1.text_fields_pairs[index][0];
    var text_field2 = this1.text_fields_pairs[index][1];
    if ( (text_field1.value == "") || (text_field2.value == "") || window.confirm(task_editor.config.confirm_window_massage) ) {
        this1.elem_divs.splice(index, 1);
        this1.remove_buttons.splice(index, 1);
        this1.text_fields_pairs.splice(index, 1);

        task_editor.object_gui.rebuild_inner_div(this1);
    }
}



//------------------------------------------------------------------------------

// task_editor.SchemaObjectGUI

/* 
   Визуализирует тип "object" для которого задана определённая JSON схема. 
   В данном приложении этот тип называется "schema object".     
*/ 

task_editor.schema_object_gui = {};

task_editor.schema_object_gui.objects = [];

task_editor.declareClass("task_editor.SchemaObjectGUI", {
    constructor: function(property_name, schema, heading, size, height_parameter, offset, changable_obj, desc) {
              /*
                 property_name - название свойства объекта в JSON схеме для описания задачи;
                 schema - JSON схема описания объекта;
                 offset - смещение визуализируемого содержимого объекта относительно
                          левого края div-элемента приложения.                                                                          
              */

              
              this.type = "schema object";   
              this.size = size;              
              this.height_parameter = height_parameter;
              
              this.schema = schema;              

              task_editor.schema_object_gui.objects.push(this);
    
              var dom_heading = (new task_editor.tLabel(heading, desc)).getDOMobject();              
              var space_label = (new task_editor.tLabel(" ")).getDOMobject();
                            
              this.edit_button = (new task_editor.tButton(task_editor.config.buttons.edit.value, task_editor.schema_object_gui.edit_handler)).getDOMobject();
              
              var top_div = (new task_editor.tDiv()).getDOMobject();             
              top_div.appendChild(dom_heading); 
              top_div.appendChild(this.edit_button);
              top_div.style.height = height_parameter + "px";  
              
              
              this.opened = false;
              
              this.content = new Object();
              
              var properties = schema.properties;
              
              for(var key in properties) if (properties.hasOwnProperty(key)) {              
                  var type = properties[key].type;                  
                  var size = task_editor.config.text_fields_size;
                  var heading = task_editor.config.properties.visualization.type.schema_object[property_name][key];
		  var desc1 = properties[key].description;

                  if (type == "string") {
                      this.content[key] = new task_editor.StringGUI(heading, size, height_parameter, "", changable_obj, desc1);                                            
                  }
                  else if (type == "boolean") {
                      this.content[key] = new task_editor.BooleanGUI(heading, height_parameter, changable_obj, desc1);
                  }
                  else if (type == "array") {
                      this.content[key] = new task_editor.ArrayGUI(heading, size, height_parameter, changable_obj, desc1);
                  }
                  else if (type == "integer") {
                      this.content[key] = new task_editor.IntegerGUI(heading, size, height_parameter, "", changable_obj, desc1);
                  }

                                    
              }              

              this.inner_div = (new task_editor.tDiv()).getDOMobject();    
              this.inner_div.style.position = "relative"; 
//              this.inner_div.style.left = offset + "px";
              this.inner_div.style.paddingLeft = offset + "px";
              this.inner_div.style.display = "none";                                   

              for(var key in this.content) if (this.content.hasOwnProperty(key)) {                                                       
                  var elem_div = this.content[key].getDOMobject();                  
                  this.inner_div.appendChild(elem_div);                                   
              }                            
              
              this.main_div = (new task_editor.tDiv()).getDOMobject();
              this.main_div.appendChild(top_div);           
              this.main_div.appendChild(this.inner_div);           
                              
              if (task_editor.objGotProperty(changable_obj, "changed")) {
                  task_editor.schema_object_gui.this1 = this;
                  this.changable_obj = changable_obj;   
              }
                                               
    },
    getDOMobject: function() {
              return this.main_div;		           	        	         
    },
    loadSchemaObject: function(schema_object) {
               
                  this.clear();                  
                  var properties = this.schema.properties;

                  for(var key in properties) if (properties.hasOwnProperty(key)) {                      
                      var type = properties[key].type;
                      var value = schema_object[key];                      
                      var gui_elem = this.content[key];
                      
                      if (type != "boolean") {                      
                          if (schema_object[key]) {
                              if (type == "string") {
                                  gui_elem.loadString(schema_object[key]);
                              }                     
                              else if (type == "array") {
                                  gui_elem.loadArray(schema_object[key]); 
                              }   
                          }
                      }
                      else if (type == "boolean") {
                          if ( (value == false) || (value == true) ) {
                              gui_elem.loadBoolean(value);
                          }
                          else if (value == undefined) {
                              value = "none";
                              gui_elem.loadBoolean(value);
                          }                                                          
                      }                                               
                  } 
           
    },    
    getSchemaObject: function() {
    
                  var schema_object = new Object(); 
    
                  for(var key in this.content) if (this.content.hasOwnProperty(key)) {
                      var type = this.content[key].type; 

                      if (type == "string") {                                
                          var string = this.content[key].getString();                                                                                   
                          if (string) {                  
                              schema_object[key] = string;
                          }                                                                            
                      }
                      else if (type == "boolean") {                                                                                
                          var boolean_ = this.content[key].getBoolean();                                                 
                          if (boolean_ != "none") { 
                              schema_object[key] = Boolean(boolean_);
                          }                                                                                                                                   
                      }                       
                      else if (type == "array") {
                          var array = this.content[key].getArray();                          
                          if (array.length > 0) {                                                                                      
                              schema_object[key] = array;
                          }                                                    
  		      }
                      else if (type == "integer") {
                          var integer = this.content[key].getInteger();                          
                          if (integer) {                                                                                      
                              schema_object[key] = integer;
                          }                                                    
                      } 
                  }
           
                  return schema_object;            
    },
    clear: function() {        
              for(var key in this.content) if (this.content.hasOwnProperty(key)) {                                                        
                  var elem_div = this.content[key].clear();
              }
              if (this.opened) {
                  this.close();   
              }                
    },     
    open: function() {
              // Показывает содержимое объекта в окне браузера

              this.inner_div.style.display = "block";                                   
              this.edit_button.innerHTML = task_editor.config.buttons.hide.value;    
              this.edit_button.onclick = task_editor.schema_object_gui.hide_handler;
              this.opened = true;
    },
    close: function() {
              // Скрывает содержимое объекта 

              this.inner_div.style.display = "none";                                   
              this.edit_button.innerHTML = task_editor.config.buttons.edit.value;    
              this.edit_button.onclick = task_editor.schema_object_gui.edit_handler; 
              this.opened = false;
    }             
});


task_editor.schema_object_gui.edit_handler = function() {
    var this1 = task_editor.getEventObject(task_editor.schema_object_gui.objects, this, "edit_button");                           
    this1.open();             
}

task_editor.schema_object_gui.hide_handler = function() {
    var this1 = task_editor.getEventObject(task_editor.schema_object_gui.objects, this, "edit_button");                           
    this1.close();             
}


		//
		// task_editor.js
		// 
                
                // Основной модуль компонента. Содержит определение
                // класса task_editor.TaskEditor, через который
                // реализуется его API.                                

/*
Uses
    class.js, utils.js, private_gui.js;          
*/ 

/*
   Для того чтобы задействовать класс task_editor.TaskEditor во внешнем приложении,
   нужно иметь в наличии ссылку на пустой div-элемент:

            // div - ссылка на пустой div-элемент
            var editor = new task_editor.TaskEditor(div);                       
            editor.run();

   Вызов функции run() осуществляет визуализацию редактора в окне браузера.
*/

task_editor.declareClass("task_editor.TaskEditor", {
    constructor: function(parent_container) {
              // parent_container - DOM-элемент, в котором будет происходить 
              // отрисовка компонента
    
              this.task = task_editor.copyObject(task_editor.config.default_data.task);
    
              this.parent_container = parent_container;
              this.changed = false;  /* 
                                        Становится true, если пользователь
                                        изменяет содержимое текстовых полей
                                        редактора. Внешнее приложение может
                                        при необходимости присвоить
                                        ему значение false.              
                                     */
                                     
              this.schema = task_editor.config.schema.main; 
              
              this.gui_elements = {};
              
              var properties = this.schema.properties;
            	                                   
              for(var key in properties) if (properties.hasOwnProperty(key)) {
                  var type = properties[key].type;
                  var desc = properties[key].description;

                  
                  if (!task_editor.arrayGotElement(task_editor.config.properties.unvisualized, key)) { 
                  
                      var heading = task_editor.config.properties.visualization.headings[key];
                      
                      var size = task_editor.config.text_fields_size;
//                      
                      var cols = task_editor.config.memo_fields_cols;
//
                      var rows = task_editor.config.memo_fields_rows;

                      var height_parameter = task_editor.config.properties.visualization.height_parameter;
                      
                      var sv = task_editor.config.properties.visualization.type.object[key];

                      var comment1 = ""; 
                      var comment2 = ""; 

                      if (sv) {
                          comment1 = sv[0]; 
                          comment2 = sv[1];                      
                      }
                     
                      if (type == "integer") {
                          this.gui_elements[key] = new task_editor.IntegerGUI(heading, size, height_parameter, "", this, desc);
                      }
                      else if (type == "string") {
                          this.gui_elements[key] = new task_editor.StringGUI(heading, size, height_parameter, "", this, desc);
                      }
                      else if (type == "array") {
                          this.gui_elements[key] = new task_editor.ArrayGUI(heading, size, height_parameter, this, desc);
                      } 
                      else if (type == "object") {
                          this.gui_elements[key] = new task_editor.ObjectGUI(heading, size, height_parameter, comment1, comment2, this, desc);
                      }
                      else if (type == "schema object") {
                          this.gui_elements[key] = new task_editor.SchemaObjectGUI(key, task_editor.config.schema.advanced[key], heading, size, height_parameter, task_editor.config.properties.visualization.schema_object_offset, this, desc);
                      }
//
                      else if (type == "memo") {
                          this.gui_elements[key] = new task_editor.MemoGUI(heading, cols, rows, height_parameter,"", task_editor.config.properties.visualization.schema_object_offset, this, desc);

                      }

                  }                                     
              }                                
    },                                          
    run: function() {
              // Визуализирует редактор в окне браузера
    
              var empty_div = (new task_editor.tDiv()).getDOMobject();
              empty_div.style.height = task_editor.config.properties.visualization.properties_top + "px";
              this.parent_container.appendChild(empty_div);
    
              for(var key in this.gui_elements) if (this.gui_elements.hasOwnProperty(key)) {
                  var dom_object = this.gui_elements[key].getDOMobject();
                  this.parent_container.appendChild(dom_object);
                  var type = this.gui_elements[key].type;                          
              }
    },
    load: function(task_string) {
              // Загружает строку task_string с JSON-описанием задачи в компонент
              
             try { 
              
                  var task = JSON.parse(task_string);
                                                                   
                  if (!task) {
                      throw new Error(task_editor.config.error.messages.JSONsyntax);
                  }
                  else if ( !task_editor.checkTask(task) ) {
                      throw new Error(task_editor.config.error.messages.jobDescriptionSyntax);
                  }
                  
                  this.clear();
                                     
                  this.task = task;                                

                  var properties = this.schema.properties;
              
                  for(var key in task) if (task.hasOwnProperty(key)) {
                      if (properties[key]) {
                          if (!task_editor.arrayGotElement(task_editor.config.properties.unvisualized, key)) {
                              var type = properties[key].type;
              
                              if (type == "integer") {
                                  this.gui_elements[key].loadInteger(task[key]);
                              }
                              else if (type == "string") {
                                  this.gui_elements[key].loadString(task[key]);
                              }
                              else if (type == "array") {
                                  this.gui_elements[key].loadArray(task[key]);
                              } 
                              else if (type == "object") {
                                  this.gui_elements[key].loadObject(task[key]);
                              }
                              else if (type == "schema object") {
                                  this.gui_elements[key].loadSchemaObject(task[key]);
                                  this.gui_elements[key].open();
                              }                          
//
                              else if (type == "memo") {
                                  this.gui_elements[key].loadMemo(task[key]);
                                  this.gui_elements[key].open();
                              }
                          } 
                      }                                 
                  } 
            
                 return true;                  
          
            } catch (e) {
                  alert(e.message);
                  return false;
            }  
    },
    text: function() {
              /*
                 Возвращает строку с описанием задачи, отвечающую текущему
                 состоянию текстовых полей и параметров по умолчанию, заданных
                 в конфигурационном файле task_editor.conf. Если для какого-либо
                 параметра не указано значение в текстовом поле, либо текстовое
                 поле вовсе отсутствует, то берётся значение по умолчанию,
                 указанное в этом файле, если таковое там имеется.                           
              */
    
              try {
      
                  var default_task = task_editor.copyObject(task_editor.config.default_data.task);
      
                  for(var key in this.gui_elements) if (this.gui_elements.hasOwnProperty(key)) {
                      var type = this.gui_elements[key].type; 

                      if (type == "integer") {
                          var integer = this.gui_elements[key].getInteger();
            
                          if (isNaN(integer) && ( this.gui_elements[key].text_field.value != "" ) ) {
                              var cause = key;
                              throw new Error(task_editor.config.error.messages.bad + cause + "!");               
                          }
                  
                          if (integer) {
                              this.task[key] = integer;
                          }
                          else if (default_task[key] != undefined) {
                              this.task[key] = default_task[key];
                          }                          
                          else {
                              delete this.task[key];
                          }                                           
                      }
                      else if (type == "string") {
                                
                          var string = this.gui_elements[key].getString();
                          
                          if (task_editor.gotQuote(string)) {
                              if (task_editor.arrayGotElement(task_editor.config.properties.quotes_admited, key)) {
                                  string = task_editor.appendSlashBeforeQuotes(string);
                              }
                              else {
                                  var cause = key;
                                  throw new Error(cause + task_editor.config.error.messages.quotes);
                              }
                          }
                                                    
                          if (string) {                  
                              this.task[key] = string;
                          }
                          else if (default_task[key] != undefined) {
                              this.task[key] = default_task[key];
                          }                              
                          else {
                              delete this.task[key];
                          }                           
                      }
                      else if (type == "array") {
                          var array = this.gui_elements[key].getArray();
                          if (array.length > 0) {   
                                                         
                               for (var i = 0; i < array.length; i++) {
                                   if (task_editor.gotQuote(array[i])) {
                                       if (task_editor.arrayGotElement(task_editor.config.properties.quotes_admited, key)) {
                                           array[i] = task_editor.appendSlashBeforeQuotes(array[i]);
                                       }
                                       else {
                                           var cause = key;
                                           throw new Error(cause + task_editor.config.error.messages.quotes);
                                       }
                                   }                                                                                             
                               }
                                                                                                                          
                              this.task[key] = array;
                          }
                          else if (default_task[key] != undefined) {
                              this.task[key] = task_editor.copyArray(default_task[key]);
                          }                           
                          else {
                              delete this.task[key];
                          }                             
                      } 
                      else if (type == "object") {
                          var object = this.gui_elements[key].getObject();
                      
                          var N = 0;                      
                          for(var key2 in object) if (object.hasOwnProperty(key2)) {
                              N++;
                          }
                      
                          if (N > 0) { 
                          
                              for(var key2 in object) if (object.hasOwnProperty(key2)) {
                              
                                   if (task_editor.gotQuote(key2)) {
                                       var cause = key;
                                       throw new Error(cause + task_editor.config.error.messages.keys);
                                   }    
                              
                                   if (task_editor.gotQuote(object[key2])) {
                                       if (task_editor.arrayGotElement(task_editor.config.properties.quotes_admited, key)) {
                                           object[key2] = task_editor.appendSlashBeforeQuotes(object[key2]);
                                       }
                                       else {
                                           var cause = key;
                                           throw new Error(cause + task_editor.config.error.messages.quotes);
                                       }
                                   }                                                             
                              
                              }
                                       
                              this.task[key] = object;
                          }
                          else if (default_task[key] != undefined) {
                              this.task[key] = task_editor.copyObject(default_task[key]);
                          }                            
                          else {
                              delete this.task[key];
                          }                            
                      }
                      else if (type == "schema object") {
                          var schema_object = this.gui_elements[key].getSchemaObject();
                      
                          var N = 0;                      
                          for(var key2 in schema_object) if (schema_object.hasOwnProperty(key2)) {
                              N++;
                          }
                      
                          if (N > 0) {                                                                  
                              this.task[key] = schema_object;
                          }                     
                          else {
                              delete this.task[key];
                          }                            
                      }                                                                                                 
                      else if (type == "memo") {
                          var memo = this.gui_elements[key].getMemo();
			  var N = 0;

                          if (default_task[key] != undefined) {
                              this.task[key] = default_task[key];
                          } else {                             
				for (var key2 in memo) if (memo.hasOwnProperty(key2)) {
                              		this.task[key] = memo;
			      		N = 1;
			      		break;
				}
				if (N == 0) {
					delete this.task[key];
				}
                          }

                      }

                  }     
             
                  if ( !task_editor.checkTask(this.task) ) {
                    
                      if ( !window.confirm(task_editor.config.error.messages.incompleteRequiredFieldsSet + task_editor.getStringOfRequiredVisualizedKeys()) ) {
                          throw -1;
                      }
                  }
             
                  var s = JSON.stringify( this.task, null, '\t' );             
                  return s;

              } catch(e) { 
                  if (e == -1) {
                      return false;
                  }
                  else {
                      alert(e.message);
                      return false;
                  }
              }
                                
    },
    clear: function() {
              // Очищает текстовые поля редактора. Визуально он возвращается 
              // в исходное состояние.
     
              this.task = task_editor.copyObject(task_editor.config.default_data.task);
              
              for(var key in this.gui_elements) if (this.gui_elements.hasOwnProperty(key)) {                                 
                  this.gui_elements[key].clear();
              }
              
              this.changed = false;
    }       
});


task_editor.getRequiredKeys = function() {

    var properties = task_editor.config.schema.main.properties;
    
    var required_keys = [];
    
    for(var key in properties) if (properties.hasOwnProperty(key)) {
        if (!properties[key].optional) {  
            required_keys.push(key);
        }
    }  

    return required_keys;
}

task_editor.getStringOfRequiredVisualizedKeys = function() {

    var required_keys = task_editor.getRequiredKeys(); 
    
    var required_keys_string = "\n"; 

    for (var i = 0; i < required_keys.length - 1; i++) {
        if ( !task_editor.arrayGotElement(task_editor.config.properties.unvisualized, key) ) { 
            required_keys_string += required_keys[i] + "\n";
        }    
    }
    required_keys_string += required_keys[required_keys.length - 1];

    return required_keys_string;
}
 
task_editor.checkTask = function(task) {
    // Если имена свойств task удовлетворяют JSON схеме для описания задачи
    // возвращает true, в противном случае false.

    var check_passed = true;

    var required_keys = task_editor.getRequiredKeys();   

    var i = 0;
    for(var key in task) if (task.hasOwnProperty(key)) { 
        if ( task_editor.arrayGotElement(required_keys, key) ) {
             i++;      
        }
    }
    
    if (i < required_keys.length) {
        check_passed = false;    
    }

    return check_passed;
}                                                                                                        		