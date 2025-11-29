// Vitest test setup file
import { vi } from 'vitest';

// Mock Inertia global
global.route = vi.fn();
