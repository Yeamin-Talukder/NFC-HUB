<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFC Data Hub - Share Your World</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#00ff88',
                    }
                }
            }
        }
    </script>
    <style>
        body { margin: 0; padding: 0; overflow: hidden; } /* Lock scroll for physics demo */
        .hero {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Let clicks pass through to canvas if needed, but we want buttons clickable */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            z-index: 10;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(to right, #fff, var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.1;
            /* Add background for text readability over physics objects */
            text-shadow: 0 5px 15px rgba(0,0,0,0.5); 
            pointer-events: auto; /* Enable text selection */
        }
        .hero p {
            font-size: 1.2rem;
            color: #ddd;
            max-width: 600px;
            margin-bottom: 2rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.8);
            pointer-events: auto; /* Enable text selection */
        }
        .cta-buttons {
            display: flex;
            gap: 1rem;
            pointer-events: auto; /* Re-enable clicks */
        }
        .btn-outline {
            background: rgba(0,0,0,0.5);
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            backdrop-filter: blur(5px);
        }
        .btn-outline:hover {
            background: var(--accent-color);
            color: #fff;
        }
        /* nav styles moved to nav.php */
        /* HOMEPAGE SPECIFIC NAV OVERRIDES */
        nav {
            background: none !important; /* Remove dark gradient so shapes are fully visible */
            pointer-events: none !important; /* Let clicks pass through to the shapes */
        }
        /* Style individual nav items for readability */
        nav > a, /* Logo */
        nav div > a:not(.btn) /* Text links (Login/Dashboard) */
        {
            pointer-events: auto !important;
            background: rgba(0, 0, 0, 0.6); /* Dark semi-transparent bg */
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.3s;
        }
        nav .btn {
            pointer-events: auto !important;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5); /* Add shadow to button */
        }
        /* Hover effects for the new pill backgrounds */
        nav > a:hover,
        nav div > a:not(.btn):hover {
            background: rgba(0, 0, 0, 0.8);
            border-color: var(--accent-color);
        }
    </style>
    <!-- Matter.js for Physics -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/matter-js/0.19.0/matter.min.js"></script>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="hero fade-in">
        <h1>Connect with<br>Just a Tap.</h1>
        <p>The modern way to share your digital identity. Create your profile, link your NFC card, and share your world instantly.</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn" style="width: auto; padding: 12px 30px;">Get Started</a>
            <a href="login.php" class="btn btn-outline" style="width: auto; padding: 12px 30px;">Login</a>
        </div>
    </div>

    <!-- Physics Canvas Container -->
    <div id="physics-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></div>

    <script>
        // Physics Engine Setup
        const Engine = Matter.Engine,
              Render = Matter.Render,
              World = Matter.World,
              Bodies = Matter.Bodies,
              Mouse = Matter.Mouse,
              MouseConstraint = Matter.MouseConstraint,
              Composite = Matter.Composite;

        const engine = Engine.create();
        const render = Render.create({
            element: document.getElementById('physics-container'),
            engine: engine,
            options: {
                width: window.innerWidth,
                height: window.innerHeight,
                wireframes: false,
                background: 'transparent' 
            }
        });

        // Disable global gravity so we can control it manually per object
        engine.world.gravity.y = 0;

        // Create Walls
        const wallOptions = { isStatic: true, render: { fillStyle: 'transparent' } };
        const ground = Bodies.rectangle(window.innerWidth/2, window.innerHeight + 50, window.innerWidth, 100, wallOptions);
        const ceiling = Bodies.rectangle(window.innerWidth/2, -50, window.innerWidth, 100, wallOptions);
        const leftWall = Bodies.rectangle(-50, window.innerHeight/2, 100, window.innerHeight, wallOptions);
        const rightWall = Bodies.rectangle(window.innerWidth+50, window.innerHeight/2, 100, window.innerHeight, wallOptions);

        World.add(engine.world, [ground, ceiling, leftWall, rightWall]);

        // Helper to create random shapes
        function createShape(x, y, gravityDir) {
            const colors = ['#00ff88', '#ffffff']; 
            
            // Responsive Size Calculation
            const isMobile = window.innerWidth < 768;
            const minSize = isMobile ? 15 : 40;
            const sizeRange = isMobile ? 25 : 50;
            const size = Math.random() * sizeRange + minSize; // 15-40px on mobile, 40-90px on desktop

            const color = colors[Math.floor(Math.random() * colors.length)];
            const renderOptions = { fillStyle: color, strokeStyle: '#1a1a1a', lineWidth: 2 };
            
            let body;
            const shapeType = Math.random();
            const commonOptions = {
                render: renderOptions,
                restitution: 0.9,
                plugin: { gravityDir: gravityDir } // Custom property for our gravity loop
            };

            if (shapeType < 0.25) {
                body = Bodies.rectangle(x, y, size, size, commonOptions);
            } else if (shapeType < 0.5) {
                body = Bodies.circle(x, y, size/2, commonOptions);
            } else if (shapeType < 0.75) {
                body = Bodies.polygon(x, y, 3, size/1.5, commonOptions);
            } else {
                const sides = Math.floor(Math.random() * 2) + 5;
                body = Bodies.polygon(x, y, sides, size/1.8, commonOptions);
            }
            return body;
        }

        const shapes = [];

        // Bottom Group (Gravity Down) - 25 shapes
        for (let i = 0; i < 25; i++) { 
            const x = Math.random() * window.innerWidth;
            const y = (Math.random() * window.innerHeight/2) + window.innerHeight/2; // Bottom half
            shapes.push(createShape(x, y, 1)); // 1 = Down
        }

        // Top Group (Gravity Up) - 25 shapes
        for (let i = 0; i < 25; i++) { 
            const x = Math.random() * window.innerWidth;
            const y = Math.random() * window.innerHeight/2; // Top half
            shapes.push(createShape(x, y, -1)); // -1 = Up
        }

        World.add(engine.world, shapes);

        // Add Mouse Interaction
        const mouse = Mouse.create(render.canvas);
        const mouseConstraint = MouseConstraint.create(engine, {
            mouse: mouse,
            constraint: {
                stiffness: 0.2,
                render: { visible: false }
            }
        });
        World.add(engine.world, mouseConstraint);

        // Keep the mouse in sync with rendering
        render.mouse = mouse;

        // Custom Gravity Loop
        Matter.Events.on(engine, 'beforeUpdate', function(event) {
            const gravityScale = 0.001; // Standard Matter.js gravity scale
            
            Composite.allBodies(engine.world).forEach(function(body) {
                if (body.isStatic || body.isSleeping) return;

                // Apply manual gravity force based on our custom direction
                // Default to 1 (down) if undefined
                const dir = (body.plugin && body.plugin.gravityDir) ? body.plugin.gravityDir : 0;
                
                if (dir !== 0) {
                    Matter.Body.applyForce(body, body.position, {
                        x: 0,
                        y: body.mass * gravityScale * dir
                    });
                }
            });
        });

        // Run the engine
        Matter.Runner.run(engine);
        Render.run(render);
        
        // (Removed respawn loop to keep original "bouncing in box" feel)

        // Handle Resize
        window.addEventListener('resize', () => {
            render.canvas.width = window.innerWidth;
            render.canvas.height = window.innerHeight;
            // Reposition walls... (simplified for now, full resizing requires regenerating walls)
        });
    </script>
    <script src="assets/js/bg-animation.js"></script>
</body>
</html>
