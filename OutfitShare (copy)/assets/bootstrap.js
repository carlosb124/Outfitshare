import { startStimulusApp } from '@symfony/stimulus-bundle';
import OutfitEditorController from './controllers/outfit_editor_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('outfit-editor', OutfitEditorController);
