document.addEventListener('DOMContentLoaded', function() {
    // Set current year and last modified date
    const currentYear = new Date().getFullYear();
    document.getElementById('current-year').textContent = currentYear;
    
    const lastModified = document.lastModified;
    document.getElementById('last-modified').textContent = lastModified;
   // Trail data
   const trails = [
       {
           id: 'coastal',
           name: 'Coastal Bluff Trail',
           difficulty: 'Easy',
           distance: '3 miles',
           duration: '2 hours',
           elevation: '200 ft',
           description: 'This scenic coastal trail offers breathtaking ocean views and gentle terrain perfect for all skill levels. The well-maintained path winds along dramatic cliffs with several lookout points to spot marine life. Perfect for families and those looking for a relaxing hike with incredible views.',
           highlights: [
               'Stunning ocean vistas',
               'Whale watching opportunities (seasonal)',
               'Accessible trail surface',
               'Wildflower displays in spring'
           ],
           image: 'coastal-trail.jpg'
       },
       {
           id: 'eagle-peak',
           name: 'Eagle Peak Summit',
           difficulty: 'Moderate',
           distance: '5.5 miles',
           duration: '4 hours',
           elevation: '1,500 ft',
           description: 'A challenging but rewarding hike to one of the region\'s most spectacular viewpoints. The trail climbs steadily through mixed forest before emerging onto rocky slopes with panoramic views. The final ascent requires some scrambling but the 360-degree summit views are worth every step.',
           highlights: [
               'Panoramic summit views',
               'Alpine wildflowers in summer',
               'Possible eagle sightings',
               'Rock formations and geological features'
           ],
           image: 'mountain-summit.jpg'
       },
       {
           id: 'hidden-falls',
           name: 'Hidden Falls Loop',
           difficulty: 'Hard',
           distance: '8 miles',
           duration: '6 hours',
           elevation: '2,200 ft',
           description: 'This full-day adventure takes you through ancient old-growth forest to a spectacular 120-foot waterfall. The trail includes river crossings, steep sections, and varied terrain. The waterfall is most impressive in spring but offers a cool retreat in summer months.',
           highlights: [
               '120-foot waterfall',
               'Old-growth forest',
               'Moss-covered boulders',
               'Fern-lined canyon'
           ],
           image: 'waterfall-trail.jpg'
       }
   ];
   
   // Display trail details if on a specific trail page
   const trailId = window.location.hash.substring(1);
   const trailDetailSection = document.querySelector('.trail-detail');
   
   if (trailDetailSection && trailId) {
       const trail = trails.find(t => t.id === trailId);
       
       if (trail) {
           document.title = `${trail.name} | Pacific Trail Hikes`;
           
           const trailDetailHTML = `
               <div class="trail-header">
                   <h2>${trail.name}</h2>
                   <div class="trail-meta">
                       <span class="difficulty ${trail.difficulty.toLowerCase()}">${trail.difficulty}</span>
                       <span class="distance">${trail.distance}</span>
                       <span class="duration">${trail.duration}</span>
                       <span class="elevation">Elevation: ${trail.elevation}</span>
                   </div>
               </div>
               
               <div class="trail-content">
                   <img src="images/${trail.image}" alt="${trail.name}" loading="lazy" class="trail-image">
                   <div class="trail-description">
                       <p>${trail.description}</p>
                       
                       <h3>Trail Highlights</h3>
                       <ul class="trail-highlights">
                           ${trail.highlights.map(highlight => `<li>${highlight}</li>`).join('')}
                       </ul>
                       
                       <a href="events.html" class="btn btn-primary">Book This Hike</a>
                   </div>
               </div>
           `;
           
           trailDetailSection.innerHTML = trailDetailHTML;
       }
   }
   
   // Trail filter functionality
   const filterButtons = document.querySelectorAll('.trail-filter button');
   const trailCards = document.querySelectorAll('.trail-card');
   
   filterButtons.forEach(button => {
       button.addEventListener('click', function() {
           const difficulty = this.dataset.filter;
           
           // Update active button
           filterButtons.forEach(btn => btn.classList.remove('active'));
           this.classList.add('active');
           
           // Filter trails
           trailCards.forEach(card => {
               if (difficulty === 'all' || card.dataset.difficulty === difficulty) {
                   card.style.display = 'block';
               } else {
                   card.style.display = 'none';
               }
           });
       });
   });
   
   // Save favorite trails to localStorage
   const favoriteButtons = document.querySelectorAll('.favorite-trail');
   
   favoriteButtons.forEach(button => {
       button.addEventListener('click', function() {
           const trailId = this.dataset.trailId;
           let favorites = JSON.parse(localStorage.getItem('favoriteTrails') || []);
           
           if (favorites.includes(trailId)) {
               favorites = favorites.filter(id => id !== trailId);
               this.textContent = '♡ Save to Favorites';
               this.classList.remove('active');
           } else {
               favorites.push(trailId);
               this.textContent = '♥ Favorited';
               this.classList.add('active');
           }
           
           localStorage.setItem('favoriteTrails', JSON.stringify(favorites));
       });
   });
   
   // Initialize favorite buttons
   function initializeFavorites() {
       const favorites = JSON.parse(localStorage.getItem('favoriteTrails') || '[]');
       
       favoriteButtons.forEach(button => {
           const trailId = button.dataset.trailId;
           
           if (favorites.includes(trailId)) {
               button.textContent = '♥ Favorited';
               button.classList.add('active');
           }
       });
   }
   
   initializeFavorites();
});
  