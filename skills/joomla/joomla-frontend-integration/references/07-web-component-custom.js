/**
 * Web Component Custom - Card Interactiva
 * Estándar W3C, encapsulado con Shadow DOM
 * Compatible con Joomla 5/6
 *
 * Uso:
 * <custom-card
 *   title="Mi Tarjeta"
 *   description="Descripción"
 *   image="url-imagen"
 *   clickable="true">
 * </custom-card>
 */

class CustomCard extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
  }

  // Propiedades observables
  static get observedAttributes() {
    return ['title', 'description', 'image', 'clickable'];
  }

  // Ciclo de vida: conectado al DOM
  connectedCallback() {
    this.render();
    this.setupEventListeners();
  }

  // Ciclo de vida: atributo cambió
  attributeChangedCallback(name, oldValue, newValue) {
    if (oldValue !== newValue) {
      this.render();
    }
  }

  // Obtener atributo
  getAttribute(name) {
    return super.getAttribute(name) || '';
  }

  // Renderizar Shadow DOM
  render() {
    const title = this.getAttribute('title') || 'Tarjeta';
    const description = this.getAttribute('description') || '';
    const image = this.getAttribute('image') || '';
    const clickable = this.getAttribute('clickable') === 'true';

    const template = `
      <style>
        :host {
          --card-bg: #fff;
          --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
          --card-border-radius: 8px;
          --card-padding: 20px;
          --primary-color: #667eea;
          --text-color: #333;
          --text-light: #666;
        }

        .card {
          background: var(--card-bg);
          border-radius: var(--card-border-radius);
          overflow: hidden;
          box-shadow: var(--card-shadow);
          transition: all 0.3s ease;
          height: 100%;
          display: flex;
          flex-direction: column;
        }

        ${clickable ? `
        .card {
          cursor: pointer;
        }

        .card:hover {
          transform: translateY(-4px);
          box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .card:active {
          transform: translateY(-2px);
        }
        ` : ''}

        .card-image {
          width: 100%;
          height: 200px;
          object-fit: cover;
          background: #f5f5f5;
        }

        .card-content {
          padding: var(--card-padding);
          flex: 1;
          display: flex;
          flex-direction: column;
        }

        .card-title {
          font-size: 1.25rem;
          font-weight: 600;
          color: var(--text-color);
          margin: 0 0 10px 0;
        }

        .card-description {
          font-size: 0.95rem;
          color: var(--text-light);
          line-height: 1.5;
          margin: 0;
          flex: 1;
        }

        .card-footer {
          padding: 10px var(--card-padding) 0;
          display: flex;
          gap: 10px;
          align-items: center;
        }

        .card-action {
          background: var(--primary-color);
          color: white;
          border: none;
          padding: 8px 16px;
          border-radius: 4px;
          cursor: pointer;
          font-size: 0.9rem;
          transition: background 0.3s ease;
        }

        .card-action:hover {
          background: #5568d3;
        }

        /* Responsive */
        @media (max-width: 480px) {
          .card-image {
            height: 150px;
          }

          .card-content {
            padding: 15px;
          }

          .card-title {
            font-size: 1.1rem;
          }
        }

        /* Tema oscuro */
        @media (prefers-color-scheme: dark) {
          :host {
            --card-bg: #2a2a2a;
            --text-color: #e0e0e0;
            --text-light: #b0b0b0;
          }
        }

        /* Animación de entrada */
        @keyframes cardEnter {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .card {
          animation: cardEnter 0.3s ease-out;
        }
      </style>

      <div class="card" role="article">
        ${image ? `<img src="${this.escapeHtml(image)}" alt="${this.escapeHtml(title)}" class="card-image">` : ''}
        <div class="card-content">
          <h3 class="card-title">${this.escapeHtml(title)}</h3>
          ${description ? `<p class="card-description">${this.escapeHtml(description)}</p>` : ''}
        </div>
        <div class="card-footer">
          <button class="card-action" aria-label="Leer más">Leer Más</button>
        </div>
      </div>
    `;

    this.shadowRoot.innerHTML = template;
  }

  // Setup de eventos
  setupEventListeners() {
    const card = this.shadowRoot.querySelector('.card');
    const button = this.shadowRoot.querySelector('.card-action');

    if (button) {
      button.addEventListener('click', (e) => {
        e.stopPropagation();
        this.dispatchEvent(new CustomEvent('card-action', {
          detail: { title: this.getAttribute('title') },
          bubbles: true,
          composed: true
        }));
      });
    }

    if (this.getAttribute('clickable') === 'true' && card) {
      card.addEventListener('click', () => {
        this.dispatchEvent(new CustomEvent('card-click', {
          detail: { title: this.getAttribute('title') },
          bubbles: true,
          composed: true
        }));
      });
    }
  }

  // Escapar HTML
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  // Método público: actualizar contenido
  setContent(title, description, image) {
    this.setAttribute('title', title);
    this.setAttribute('description', description);
    if (image) this.setAttribute('image', image);
  }

  // Método público: obtener datos
  getContent() {
    return {
      title: this.getAttribute('title'),
      description: this.getAttribute('description'),
      image: this.getAttribute('image')
    };
  }
}

// Registrar Web Component
customElements.define('custom-card', CustomCard);

// Exportar para uso modular
if (typeof module !== 'undefined' && module.exports) {
  module.exports = CustomCard;
}
