
<!doctype html>

<html lang="en" class="no-js">

    <head>
        <meta charset="UTF-8" />
        <link rel="shortcut icon" href="{{ asset('jafar/img/nda-logo.png') }}" type="image/x-icon" />

        <title>NDA|App</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="NDA Postgraduate School" />
        <meta name="keywords" content="education, nda, pg, postgraduate, school, :target, pseudo-class" />
        <meta name="author" content="Torkuma Agber" />
        <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('jafar/css/demo.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('jafar/css/style2.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('jafar/css/demo2.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('jafar/css/style3.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('jafar/css/animate-custom.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('jafar/css/ndaforms.css') }}" />

    </head>

    <body onload='loadCategories();'>

        <div class="container">

            <div class="codrops-top">
                <a href="https://spgs.nda.edu.ng" target="_blank">

                    <strong>&laquo; PG PORTAL</strong>
                </a>
                <span class="right">
                    <a href="https://spgs.nda.edu.ng" target="_blank">
                        <strong>NDA POST GRADUATE SCHOOL &raquo;</strong>
                    </a>
                </span>
                <div class="clr"></div>
            </div>
            <header style="width: 1000px; text-align: left; margin: 0 auto; border-style: none; height: auto; padding: 0px; background: none repeat scroll 0 0 #FFFFFF;
                    background-color:green; ">
                <div style="display:table-cell; vertical-align: middle; float: right; border-style: none; width: 450px; color: #008B8B; margin-top: 5px;">
                    <!-- applicants details go here -->
                                </div>
                <div style='display: table; height: auto; border-style: none;'>
                    <img style="float: left;width: 60%" src="{{ asset('jafar/img/nda-logo.png') }}">
                    <div style="display:table-cell; vertical-align: middle;">
                        <h1 style=""> Postgraduate School </h1>
                        <h1 style='font-size: x-large; line-height: 8pt;'><span>Nigerian Defence Academy</span></h1>
                    </div>

                </div>

            </header>

            <style>

                #remainder {
                    -webkit-animation: color-change .75s infinite;
                    -moz-animation: color-change .75s infinite;
                    -o-animation: color-change .75s infinite;
                    -ms-animation: color-change .75s infinite;
                    animation: color-change .75s infinite;
                }

                @-webkit-keyframes color-change {
                    0% { color: red; }
                    25% { color: yellow; }
                    50% { color: blue; }
                    75% { color: green; }
                    100% { color: red; }
                }
                @-moz-keyframes color-change {
                    0% { color: red; }
                    25% { color: yellow; }
                    50% { color: blue; }
                    75% { color: green; }
                    100% { color: red; }
                }
                @-ms-keyframes color-change {
                    0% { color: red; }
                    25% { color: yellow; }
                    50% { color: blue; }
                    75% { color: green; }
                    100% { color: red; }
                }
                @-o-keyframes color-change {
                    0% { color: red; }
                    25% { color: yellow; }
                    50% { color: blue; }
                    75% { color: green; }
                    100% { color: red; }
                }
                @keyframes color-change {
                    0% { color: red; }
                    25% { color: yellow; }
                    50% { color: blue; }
                    75% { color: green; }
                    100% { color: red; }
                }
            </style>

            <div id="maincontainer">
                <div id= "content">

                    <style>

                        .fac{
                                font-weight: bolder;
                                font-size: large;
                            }

                            .dept{
                                font-weight: bold;
                                list-style: lower-alpha;
                                font-size: medium;
                            }

                            .prog{
                                //font-size: small;
                                list-style: lower-roman;
                                font-weight: normal;
                            }

                            ol > li {
                            margin-left: 20px;
                        }

                        li > lo {
                            margin-top: 20px;
                        }
                        ol, p {
                            text-align: justify;
                        }
                        dt {
                            font-weight: bolder;
                            margin-top: 10px;
                        }

                        ol{

                        list-style-position: outside;
                        list-style-type:square;
                        margin-left: 15px;

                        }
                    </style>

                    <fieldset style="text-align: justify;">

                        @yield('content')

                    </fieldset>

                </div>

                <!-- begin footer -->
                <div style="float: bottom;  width: auto; border: none; bottom: 0; height: 50px; line-height: 50px;margin-top: 20px; margin-bottom: 20px; font-size: large; border-radius: 20px/10px; background-color: green;">
                    Copyright &copy; 2023 Nigerian Defence Academy. All rights reserved.
                </div>

                    @include('appmenu')

            </div>
        </div>
    </body>
</html>
