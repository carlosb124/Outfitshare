import { Controller } from '@hotwired/stimulus';


/*
 * Outfit Editor Controller
 * Handles the "Canvas" interaction:
 * - Clicking an item in the sidebar adds it to the canvas.
 * - Clicking an item in the canvas removes it.
 * - Syncs the state with the hidden Symfony form checkboxes.
 */
export default class extends Controller {
    static targets = ["canvas", "sidebarItem", "checkbox"];
    static values = {
        maxItems: Number
    }

    connect() {
        console.log('Outfit Editor Connected! 🎨');
        console.log('Targets:', this.sidebarItemTargets, this.checkboxTargets);
        this.updateCanvasFromForm();
    }

    // Called when clicking an item in the sidebar
    toggleItem(event) {
        console.log('Clicked item:', event.currentTarget.dataset.id);
        const itemId = event.currentTarget.dataset.id;
        const checkbox = this.checkboxTargets.find(cb => cb.value === itemId);
        console.log('Found checkbox:', checkbox);

        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            this.updateCanvasState();
            this.updateSidebarState();
        }
    }

    // Called when clicking an item in the canvas (to remove)
    removeItem(event) {
        const itemId = event.currentTarget.dataset.id;
        const checkbox = this.checkboxTargets.find(cb => cb.value === itemId);

        if (checkbox) {
            checkbox.checked = false;
            this.updateCanvasState();
            this.updateSidebarState();
        }
    }

    /* 
     * Renders the items in the canvas based on checked checkboxes.
     * In a real React/Vue app this would comprise the state.
     * Here we just rebuild the DOM or toggle visibility.
     */
    updateCanvasState() {
        this.canvasTarget.innerHTML = ''; // Clear canvas

        const checkedBoxes = this.checkboxTargets.filter(cb => cb.checked);

        if (checkedBoxes.length === 0) {
            this.canvasTarget.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-gray-300">
                    <i class="bi bi-stars text-6xl mb-4"></i>
                    <p class="font-medium text-lg">Tu creación empieza aquí</p>
                    <p class="text-sm">Toca prendas a la izquierda para añadir</p>
                </div>
            `;
            return;
        }

        checkedBoxes.forEach(cb => {
            const sidebarItem = this.sidebarItemTargets.find(item => item.dataset.id === cb.value);
            if (sidebarItem) {
                const imgSrc = sidebarItem.dataset.image;
                const name = sidebarItem.dataset.name;

                // Create Canvas Element (Draggable-like visual)
                const el = document.createElement('div');
                el.className = 'relative group w-32 h-32 md:w-48 md:h-48 flex-shrink-0 cursor-pointer hover:scale-105 transition-transform duration-300';
                el.dataset.id = cb.value;
                el.dataset.action = 'click->outfit-editor#removeItem';

                el.innerHTML = `
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                       <img src="${imgSrc}" class="max-w-full max-h-full drop-shadow-2xl filter object-contain" alt="${name}">
                    </div>
                    <div class="absolute top-0 right-0 opacity-0 group-hover:opacity-100 transition-opacity bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-md">
                        <i class="bi bi-x"></i>
                    </div>
                `;

                this.canvasTarget.appendChild(el);
            }
        });
    }

    updateSidebarState() {
        const checkedValues = this.checkboxTargets.filter(cb => cb.checked).map(cb => cb.value);

        this.sidebarItemTargets.forEach(item => {
            if (checkedValues.includes(item.dataset.id)) {
                item.classList.add('ring-4', 'ring-neon-lime', 'opacity-50', 'grayscale');
            } else {
                item.classList.remove('ring-4', 'ring-neon-lime', 'opacity-50', 'grayscale');
            }
        });
    }

    // Initial sync on load (in case of validation error reload)
    updateCanvasFromForm() {
        this.updateCanvasState();
        this.updateSidebarState();
    }
}
