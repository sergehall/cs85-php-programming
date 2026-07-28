import assert from 'node:assert/strict';
import test from 'node:test';

import { connectionSummary } from '../../resources/js/ai-connections.js';

test('connectionSummary distinguishes complete and partial provider health', () => {
    assert.equal(connectionSummary(4, 4), 'All 4 models connected');
    assert.equal(connectionSummary(3, 4), '3 of 4 models connected');
});
