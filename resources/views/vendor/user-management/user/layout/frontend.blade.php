<!doctype html>
<html lang="en" data-critters-container>
  <head>
    <meta charset="utf-8">
    <title>Alexei Cioina</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="image/x-icon" href="favicon.ico" rel="icon">
    <style type="text/css">
      .global-loader {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1;
        width: 100%;
        height: 100%;
        opacity: 1;
        will-change: transform;
        transition: opacity 0.5s ease-in-out;
      }

      .global-loader-fade-in {
        will-change: transform;
        opacity: 0;
      }

      .global-loader-hidden {
        display: none;
      }

      .global-loader h1 {
        font-family: 'Helvetica Neue', Helvetica, sans-serif;
        font-weight: normal;
        font-size: 18px;
        letter-spacing: 0.04rem;
        white-space: pre;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        background-image: repeating-linear-gradient(
          to right,
          #f44336,
          #9c27b0,
          #3f51b5,
          #03a9f4,
          #009688,
          #8bc34a,
          #ffeb3b,
          #ff9800
        );
        background-size: 750% auto;
        background-position: 0 100%;
        animation: gradient 20s infinite;
        animation-fill-mode: forwards;
        animation-timing-function: linear;
      }

      .night-bg {
        background-color: #141414;
      }

      @keyframes gradient {
        0% {
          background-position: 0 0;
        }

        100% {
          background-position: -750% 0;
        }
      }
    </style>
  </head>
  <body>
    <app-root></app-root>
    <div id="globalLoader" class="global-loader">
      <h1>loading...</h1>
    </div>
  <link rel="modulepreload" href="chunk-LKZOJT5P.js"><link rel="modulepreload" href="chunk-KY565OHP.js"><link rel="modulepreload" href="chunk-V5RIWGZD.js"><link rel="modulepreload" href="chunk-EFZTD2N2.js"><link rel="modulepreload" href="chunk-GZOZOPYV.js"><link rel="modulepreload" href="chunk-DZ3RWWRR.js"><link rel="modulepreload" href="chunk-HJBKCWL3.js"><link rel="modulepreload" href="chunk-MKVVA7KP.js"><link rel="modulepreload" href="chunk-U5TYFZYN.js"><link rel="modulepreload" href="chunk-3J35EFHR.js"><script src="polyfills-EONH2QZO.js" type="module"></script><script src="main-QPOA4RD6.js" type="module"></script></body>
  <script>
    const isStyleThemeModelKey = window.localStorage.getItem('StyleThemeModelKey');
    if (isStyleThemeModelKey && isStyleThemeModelKey === 'dark') {
      document.getElementById('globalLoader').className += ' night-bg';
    }
  </script>
</html>
