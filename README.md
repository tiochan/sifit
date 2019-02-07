# SIFIT
SIFIT stands for Sysops Intelligence For IT.


##Introduction
Is an application to define and represent reports and dashboards using wysiwyg rich-text editors and special pieces called "TAGs". One TAG belongs to a type, and is going to be **calculated on demand** and its value will replace the TAG name on the report or dashboard. Usually the TAGs are used between "{ }"

Those TAG types, are defined by a master class which is inherited to create any other TAG types that we need.
By default you will find a set of pre-defined TAG types that are described in this doc.

TAGs can contains other TAGs, creating a tool with infinite options.

You can use the output of one TAG as input for another, for example, use a TAG of type query as value for a Graph, and it will represent the resulting data. If there is not defined a special representation for a query TAG, will represent the values as a table.

Also you can use TAGs into the value definition of other TAGs, for example, into a Query TAG you can insert other TAGs as admin. As mentioned before, the TAGs are evaluated in depth, so, the Query will not be evaluated until are evaluated all the TAGs it contains, and this process is recursive.

TAGs **can also contain parameters**, that are passed through the calls and defined where the TAG is defined. For example:
```
	{BUDGET_PIE_GRAPH|ID=5;width=250;height=150;show_legend=false;top=30;bottom=30;left=30;right=30;refresh_time=9}
```
> refresh_time is an special parameter which make sense in dashboard, and that will generate a JQuery object to get the content dynamically for this TAG. In reports does not make sense, because they are generated just once and sent by ... email?

For example, we can define a TAG of type "query", called "GET_USER_EMAIL" with this value:
```
	SELECT email FROM users WHERE id_user = '{USER_ID}'
```
Here, USER_ID is a TAG of type "System var", which means that will be replaced by the content of a global variable called "USER_ID", and it means that each time the report (or dashboard) is going to be represented, will use the ID of the USER that is generating the report (or dashboard).

To obtaing the value of a TAG, the execution for each TAG is recursive: if a TAG contains other TAGs, first will evaluate the TAGs that contains and once those are evaluated are replaced into the containing TAG.

For example,
We can define a new REPORT, which allow html content. We name it "USER_GREETINGS" and add this content:

>	"Hello {USER_NAME}, welcome to SIFIT.<br>
>	 I'm pleased to see you here. <br>
>	 <br>
>	 Tonight I will send you useful info to your email address: {GET_USER_EMAIL}."

In this example, when the report USER_GREETING is going to be executed, will replace one by one each TAG, and recursively in depth.

## Flow diagram
So, following this example:
1. Find tags at this level. To do that will look for "{ < anything > }", and finds 2 TAGs:
--> USER_NAME
--> GET_USER_EMAIL

2. Foreach TAG, calculate its value an replace into the current level:

2.1. First TAG: USER_NAME

2.1.1. Get value for USER_NAME, which is a TAG of type "System Var", and is going to return the content of a global var called "$USER_NAME", and in this case: tiochan.

2.1.2. Replace on the report "USER_NAME" for value "tiochan".

2.2. Next TAG: GET_USER_EMAIL

2.2.1. Get value for GET_USER_EMAIL. The get_value method is called for this tag instance, which is a query.

The query tag will do the same, first look for other sub-TAGs:
```
	SELECT email FROM users WHERE id_user = '{USER_ID}'
```
Contains a sub-tag called "USER_ID", which is a "System var", and is going to be replaced for its value (p.e. my user ID is 123). Then will execute the query:
```
	SELECT email FROM users WHERE id_user = '123'
```
The query returns "tiochan@gmail.com". Come back and replace this value on the report for the TAG name:

>	"Hello tiochan, welcome to SIFIT.<br>
>	 I'm pleased to see you here. <br>
>	 <br>
>	 I will send you tonight useful info tonight to your email address: tiochan@gmail.com."


## List of TAGs
But, which kind of TAGs can I use?

This is a reduced list of pre-defined TAG types that you can find here.
You can add yourselves inheriting the master class:

- **Graphs**, of many differents types (bar, line, pie, rotated, with series, ...)
- **Constants**, which value does not change
- **HTML**, which allows you to add rich-text
- **HTTP**, gets the content from a URL
- **HTTP Extract**, parse the content from a URL
- **Query**, which will execute a SQL sentence
- **Php code**, this means that you can add your own PHP code here and will be executed on the fly
- **System commands**, as you can imagine, execute anything executable on your system
- **Search**, to extract parts of a given input
- **And more...


This product is still under development. Feel free to test yourself, and send any feedback.
