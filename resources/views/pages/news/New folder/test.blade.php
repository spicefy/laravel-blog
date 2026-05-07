<!DOCTYPE html>
<html>
<head>
    <title>Tailwind Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center h-screen bg-gray-100">

    <div class="p-10 bg-white shadow rounded-xl text-center">
        <h1 class="text-4xl font-bold text-blue-600">
            Tailwind v4 Works 🚀
        </h1>
    </div>

     <h1 class="sr-only">Latest Education News in Kenya – Kusoma</h1>
<div class="bg-royal text-white p-6 rounded-xl">
    Tailwind is working 🚀
</div>

<div class="bg-red-500 text-white text-lg p-4">
    Tailwind Core Test
</div>
  
  <div class="flex items-center gap-4 mb-6">
    <h2 class="font-display font-semibold text-xl text-navy shrink-0">Categories</h2>
    <div class="flex-1 h-px bg-kborder"></div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-12">

        <div class="bg-white border border-kborder rounded-xl overflow-hidden">

      
      <div class="bg-green-900 px-4 py-3.5 flex items-center justify-between">

        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
          <i class="fas fa-graduation-cap text-white/75 text-xs"></i>

          <a href="http://192.168.100.15:800/category/education-news" class="hover:underline">
            Education News
          </a>
        </h3>

                <span class="text-[11px] font-semibold bg-white/20 text-white rounded-full px-2.5 py-0.5">
          3 posts
        </span>
        
      </div>

      
      <ul class="divide-y divide-kborder">

                <li class="px-4 py-3 hover:bg-blue-100 transition-all duration-200">

          <article>

            <a href="http://192.168.100.15:800/news/sample-education-news-article-1"
               class="text-[13.5px] font-medium text-kgreen leading-snug block mb-1.5 hover:underline">

              Sample Education News Article 1

            </a>

            <div class="flex gap-3 text-xs text-muted">

              <span class="text-green-900 font-medium">
                By Admin User
              </span>

              <span>•</span>

              <span>
                Apr 2, 2026
              </span>

                            <span>•</span>
              <span>1 min read</span>
              
            </div>

          </article>

        </li>
                <li class="px-4 py-3 hover:bg-blue-100 transition-all duration-200">

          <article>

            <a href="http://192.168.100.15:800/news/sample-education-news-article-2"
               class="text-[13.5px] font-medium text-kgreen leading-snug block mb-1.5 hover:underline">

              Sample Education News Article 2

            </a>

            <div class="flex gap-3 text-xs text-muted">

              <span class="text-royal font-medium">
                By Admin User
              </span>

              <span>•</span>

              <span>
                Mar 27, 2026
              </span>

                            <span>•</span>
              <span>1 min read</span>
              
            </div>

          </article>

        </li>
                <li class="px-4 py-3 hover:bg-blue-100 transition-all duration-200">

          <article>

            <a href="http://192.168.100.15:800/news/sample-education-news-article-3"
               class="text-[13.5px] font-medium text-kgreen leading-snug block mb-1.5 hover:underline">

              Sample Education News Article 3

            </a>

            <div class="flex gap-3 text-xs text-muted">

              <span class="text-green-900 font-medium">
                By Admin User
              </span>

              <span>•</span>

              <span>
                Mar 10, 2026
              </span>

                            <span>•</span>
              <span>1 min read</span>
              
            </div>

          </article>

        </li>
        
      </ul>

      
</body>
</html>