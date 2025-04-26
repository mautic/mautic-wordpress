document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');

  // Detect if current page is inside the "dev" folder
  const isDevPage = window.location.pathname.includes('/dev/');

  const sections = [
    {
      title: 'Get Started',
      links: [
        { title: 'Installation & Activation', url: 'installation.html' },
        { title: 'Plugin Settings', url: 'settings.html' },
        { title: 'Tracking Script Behavior', url: 'tracking.html' },
        { title: 'Shortcodes & Usage', url: 'shortcodes.html' }
      ]
    },
    {
      title: 'Developers',
      links: [
        { title: 'Hooks & Functions', url: 'dev/hooks-and-functions.html' },
        { title: 'Environment', url: 'dev/environment.html' }
      ]
    }
  ];

  let html = '';

  sections.forEach(section => {
    html += `<div class="sidebar-section">`;
    html += `<h2>${section.title}</h2><ul>`;
    section.links.forEach(link => {
      let linkUrl = link.url;
      if (isDevPage) {
        if (!linkUrl.startsWith('dev/')) {
          // Link is outside dev/, go up one level
          linkUrl = '../' + link.url;
        } else {
          // Link is inside dev/, remove dev/ prefix
          linkUrl = link.url.replace('dev/', '');
        }
      }
      html += `<li><a href="${linkUrl}">${link.title}</a></li>`;
    });
    html += `</ul></div>`;
  });

  sidebar.innerHTML = html;

  // Highlight active link
  const currentPage = window.location.pathname.split('/').pop();

  // If current page is index.html or blank, skip highlighting
  if (currentPage === "index.html" || currentPage === "") {
    return;
  }

  sidebar.querySelectorAll('a').forEach(link => {
    const linkPage = link.getAttribute('href').split('/').pop();
    if (linkPage === currentPage) {
      link.classList.add('active');
    }
  });
});
